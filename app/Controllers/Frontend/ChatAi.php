<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\BeritaModel;
use App\Models\DokumentasiModel;
use App\Models\PpidDokumenModel;
use App\Models\AgendaModel;

class ChatAi extends BaseController
{
    private $assistants = [
        'siska' => [
            'name' => 'Siska',
            'model' => 'gemini-2.5-flash',
            'description' => 'Standard Gemini',
            'url' => 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent'
        ],
        'lita' => [
            'name' => 'Lita',
            'model' => 'gemini-2.5-flash-lite',
            'description' => 'Gemini Lite',
            'url' => 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent'
        ],
        'gema' => [
            'name' => 'Gema',
            'model' => 'models/gemma-3-27b-it',
            'description' => 'Gemma 27B',
            'url' => 'https://generativelanguage.googleapis.com/v1beta/models/gemma-3-27b-it:generateContent'
        ],
        'gita' => [
            'name' => 'Gita',
            'model' => 'models/gemma-3-12b-it',
            'description' => 'Gemma 12B',
            'url' => 'https://generativelanguage.googleapis.com/v1beta/models/gemma-3-12b-it:generateContent'
        ],
        'gavi' => [
            'name' => 'Gavi',
            'model' => 'models/gemma-3-4b-it',
            'description' => 'Gemma 4B',
            'url' => 'https://generativelanguage.googleapis.com/v1beta/models/gemma-3-4b-it:generateContent'
        ],
        'gara' => [
            'name' => 'Gara',
            'model' => 'models/gemma-3-1b-it',
            'description' => 'Gemma 1B',
            'url' => 'https://generativelanguage.googleapis.com/v1beta/models/gemma-3-1b-it:generateContent'
        ]
    ];

    public function getAssistants()
    {
        return $this->response->setJSON([
            'status' => 'success',
            'assistants' => $this->assistants
        ]);
    }

    public function ask()
    {
        $message = $this->request->getPost('message');
        $assistantKey = $this->request->getPost('assistant') ?? 'siska';

        // Validate assistant, default to 'siska' if invalid
        if (!array_key_exists($assistantKey, $this->assistants)) {
            $assistantKey = 'siska';
        }

        $apiKey = getenv('GEMINI_API_KEY');

        if (empty($apiKey) || $apiKey === "YOUR_API_KEY_HERE") {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'API Key belum dikonfigurasi. Mohon hubungi admin.'
            ]);
        }

        if (empty($message)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Pesan tidak boleh kosong.'
            ]);
        }

        // Get relevant context from database (RAG)
        $context = $this->getRelevantContext($message);

        // Updated system instruction for concise responses
        $systemInstruction = "Anda adalah asisten virtual Bappeda Bungo. Berikan jawaban RINGKAS, LANGSUNG KE POIN INTI, maksimal 3-4 kalimat. Gunakan data yang diberikan jika relevan. Fokus pada informasi Bappeda Bungo, perencanaan pembangunan, dan layanan publik Kabupaten Bungo. Gunakan Bahasa Indonesia yang sopan dan profesional.";

        // Build prompt with context
        $prompt = $systemInstruction;
        if (!empty($context)) {
            $prompt .= "\n\nData Relevan dari Database:\n" . $context;
        }
        $prompt .= "\n\nPertanyaan User: " . $message;

        // Cache Key generation based on message and assistant
        $cacheKey = 'ai_response_' . $assistantKey . '_' . md5(strtolower(trim($message)));
        $cache = \Config\Services::cache();

        if ($cachedResponse = $cache->get($cacheKey)) {
             return $this->response->setJSON([
                'status' => 'success',
                'response' => $cachedResponse,
                'cached' => true
            ]);
        }

        // Use selected assistant's model URL
        $url = $this->assistants[$assistantKey]['url'] . "?key=" . $apiKey;

        $payload = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ]
        ];

        $maxRetries = 3;
        $attempt = 0;
        $httpCode = 0;
        $response = null;

        do {
            $attempt++;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 429 && $attempt < $maxRetries) {
                sleep(2); // Wait 2 seconds before retrying
            } else {
                break;
            }
        } while ($attempt < $maxRetries);

        if ($httpCode !== 200) {
            // Log the actual error response for debugging
            log_message('error', 'Gemini API Error - HTTP ' . $httpCode . ': ' . $response);
            
            $errorMsg = 'Gagal terhubung ke layanan AI. Kode Error: ' . $httpCode;
            if ($httpCode === 429) {
                $errorMsg = 'Layanan AI sedang sibuk (Rate Limit). Silakan coba beberapa saat lagi, atau Anda bisa coba pilih asisten lain yang tersedia.';
            }

            return $this->response->setJSON([
                'status' => 'error',
                'message' => $errorMsg
            ]);
        }

        $data = json_decode($response, true);
        $aiResponse = $data['candidates'][0]['content']['parts'][0]['text'] ?? "Maaf, saya tidak dapat memproses permintaan Anda saat ini.";

        // Save to cache for 1 hour
        if (!empty($data['candidates'])) {
            $cache->save($cacheKey, $aiResponse, 3600);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'response' => $aiResponse,
            'debug_prompt' => $prompt
        ]);
    }

    /**
     * Get relevant context from database based on user message (RAG)
     */
    private function getRelevantContext($message)
    {
        $context = [];
        $keywords = $this->extractKeywords($message);
        
        if (empty($keywords)) {
            return '';
        }

        // Search Berita
        $beritaModel = new BeritaModel();
        $berita = $beritaModel->select('judul, LEFT(isi_berita, 500) as isi_berita, tanggal')
                              ->groupStart();
        foreach ($keywords as $i => $keyword) {
            if ($i > 0) $beritaModel->orLike('judul', $keyword);
            else $beritaModel->like('judul', $keyword);
        }
        $berita = $beritaModel->groupEnd()
                              ->orderBy('tanggal', 'DESC')
                              ->limit(3)
                              ->find();
        
        if (!empty($berita)) {
            $context[] = "BERITA TERKAIT:";
            foreach ($berita as $item) {
                $cleanContent = strip_tags($item['isi_berita']);
                $context[] = "- {$item['judul']} ({$item['tanggal']})\n  Isi: {$cleanContent}...";
            }
        }

        // Search Dokumentasi
        $dokumentasiModel = new DokumentasiModel();
        $dokumentasi = $dokumentasiModel->select('judul, LEFT(deskripsi, 500) as deskripsi, tanggal')
                                       ->groupStart();
        foreach ($keywords as $i => $keyword) {
            if ($i > 0) $dokumentasiModel->orLike('judul', $keyword);
            else $dokumentasiModel->like('judul', $keyword);
        }
        $dokumentasi = $dokumentasiModel->groupEnd()
                                       ->orderBy('tanggal', 'DESC')
                                       ->limit(3)
                                       ->find();
        
        if (!empty($dokumentasi)) {
            $context[] = "\nDOKUMENTASI KEGIATAN:";
            foreach ($dokumentasi as $item) {
                $cleanDeskripsi = strip_tags($item['deskripsi']);
                $context[] = "- {$item['judul']} ({$item['tanggal']})\n  Deskripsi: {$cleanDeskripsi}...";
            }
        }

        // Search PPID Dokumen
        $ppidModel = new PpidDokumenModel();
        $ppid = $ppidModel->select('judul_dokumen, LEFT(deskripsi, 500) as deskripsi, tgl_upload')
                         ->groupStart();
        foreach ($keywords as $i => $keyword) {
            if ($i > 0) $ppidModel->orLike('judul_dokumen', $keyword);
            else $ppidModel->like('judul_dokumen', $keyword);
        }
        $ppid = $ppidModel->groupEnd()
                         ->orderBy('tgl_upload', 'DESC')
                         ->limit(3)
                         ->find();
        
        if (!empty($ppid)) {
            $context[] = "\nDOKUMEN PPID:";
            foreach ($ppid as $item) {
                $cleanDeskripsi = strip_tags($item['deskripsi']);
                $context[] = "- {$item['judul_dokumen']} ({$item['tgl_upload']})\n  Deskripsi: {$cleanDeskripsi}...";
            }
        }

        // Search Agenda
        $agendaModel = new AgendaModel();
        $agenda = $agendaModel->select('judul, LEFT(deskripsi, 500) as deskripsi, tgl_pelaksanaan, lokasi')
                             ->where('tgl_pelaksanaan >=', date('Y-m-d'))
                             ->groupStart();
        foreach ($keywords as $i => $keyword) {
            if ($i > 0) $agendaModel->orLike('judul', $keyword);
            else $agendaModel->like('judul', $keyword);
        }
        $agenda = $agendaModel->groupEnd()
                             ->orderBy('tgl_pelaksanaan', 'ASC')
                             ->limit(3)
                             ->find();
        
        if (!empty($agenda)) {
            $context[] = "\nAGENDA MENDATANG:";
            foreach ($agenda as $item) {
                $cleanDeskripsi = strip_tags($item['deskripsi']);
                $context[] = "- {$item['judul']} ({$item['tgl_pelaksanaan']}) di {$item['lokasi']}\n  Deskripsi: {$cleanDeskripsi}...";
            }
        }

        return implode("\n", $context);
    }

    /**
     * Extract keywords from user message
     */
    private function extractKeywords($message)
    {
        // Remove common words (stopwords)
        $stopwords = ['apa', 'yang', 'adalah', 'ada', 'di', 'ke', 'dari', 'untuk', 'dengan', 'pada', 'ini', 'itu', 'dan', 'atau', 'saya', 'kamu', 'kami'];
        
        // Convert to lowercase and split into words
        $words = preg_split('/\s+/', strtolower($message));
        
        // Filter out stopwords and short words
        $keywords = array_filter($words, function($word) use ($stopwords) {
            return strlen($word) > 3 && !in_array($word, $stopwords);
        });
        
        // Return unique keywords
        return array_unique(array_values($keywords));
    }
}
