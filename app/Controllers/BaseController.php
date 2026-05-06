<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\IdentitasModel;
use CodeIgniter\I18n\Time;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers.
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation.
     *
     * @var list<string>
     */
    protected $helpers = ['timeAgo'];

    /**
     * Shared data for views
     * @var array
     */
    protected $data = [];

    /**
     * Website identity data
     * @var array|null
     */
    protected $identitas;

    /**
     * User session instance
     * @var \CodeIgniter\Session\Session
     */
    protected $session;

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload Services
        $this->session = session();

        // 1. Load Website Identity (With Fallback)
        $identitasModel  = new IdentitasModel();
        $this->identitas = $identitasModel->first() ?? [
            'nama_website' => 'Bappeda Website',
            'description'  => '',
            'keywords'     => '',
            'logo'         => '',
            'favicon'      => ''
        ];

        // 2. Setup URL Assets
        $logo_url = base_url('uploads/favicon/logo.png');
        if (!empty($this->identitas['logo'])) {
            $logo_url = filter_var($this->identitas['logo'], FILTER_VALIDATE_URL) 
                ? $this->identitas['logo'] 
                : base_url('uploads/identitas/' . $this->identitas['logo']);
        }

        $favicon_url = base_url('uploads/favicon/favicon.ico');
        if (!empty($this->identitas['favicon'])) {
            $favicon_url = filter_var($this->identitas['favicon'], FILTER_VALIDATE_URL) 
                ? $this->identitas['favicon'] 
                : base_url('uploads/identitas/' . $this->identitas['favicon']);
        }

        // 3. Global Meta SEO Data
        $this->data['meta'] = [
            'title'        => esc($this->identitas['nama_website']),
            'description'  => esc($this->identitas['description']),
            'keywords'     => esc($this->identitas['keywords']),
            'author'       => esc($this->identitas['nama_website']),
            'image'        => $logo_url,
            'favicon'      => $favicon_url,
            'type'         => 'website',
            'prefix_title' => true
        ];

        $this->data['logo_url'] = $logo_url;

        // 4. Automated Visitor Tracking (Front-end only)
        $uri = service('uri');
        if ($uri->getSegment(1) !== 'roomloki') {
            $this->trackVisitor();
        }

        // 5. Global Time Information
        $tanggal = new Time('now', 'Asia/Jakarta', 'id_ID'); 
        $this->data['tanggal_sekarang'] = $tanggal->toLocalizedString('EEEE, d MMMM yyyy');

        // 6. Secure Data Transfer to All Views
        // Di sini kita TIDAK menggunakan 'raw' untuk alasan keamanan (mencegah XSS)
        service('renderer')->setData([
            'identitas'        => $this->identitas,
            'meta'             => $this->data['meta'],
            'logo_url'         => $this->data['logo_url'],
            'tanggal_sekarang' => $this->data['tanggal_sekarang']
        ]);
    }

    /**
     * Record visitor logs with bot filtering
     */
    protected function trackVisitor()
    {
        // Don't track if clearly in admin area
        if (service('uri')->getSegment(1) === 'roomloki') return;

        // Get IP with a more robust method
        $ip = $this->request->getIPAddress();

        // Strict Bot/Crawler Filter
        $agent = (string)$this->request->getUserAgent();
        $botPattern = '/(bot|crawl|spider|slurp|curl|wget|python|facebookexternalhit|GoogleOther|SiteExploration)/i';
        if (preg_match($botPattern, $agent)) return;

        $db      = \Config\Database::connect();
        $builder = $db->table('visitor_logs');
        $tanggal = date('Y-m-d');

        // Check unique visitor for today
        $exists = $builder
            ->where(['ip_address' => $ip, 'tanggal' => $tanggal])
            ->get()
            ->getRow();

        if (!$exists) {
            $builder->insert([
                'ip_address' => $ip,
                'user_agent' => substr($agent, 0, 255),
                'url'        => current_url(),
                'referer'    => $this->request->getServer('HTTP_REFERER') ?? 'Direct',
                'tanggal'    => $tanggal
            ]);
        }
    }

    /**
     * Record total views for specific news articles
     */
    protected function trackBeritaView($id_berita)
    {
        if (!$id_berita) return;

        $tanggal = date('Y-m-d');
        $db      = \Config\Database::connect();
        $builder = $db->table('berita_views');

        // Check if entry for today exists
        $exists = $builder
            ->where(['id_berita' => (int)$id_berita, 'tanggal' => $tanggal])
            ->get()
            ->getRow();

        if ($exists) {
            $builder->where('id', $exists->id)
                    ->increment('jumlah_view', 1);
        } else {
            $builder->insert([
                'id_berita'   => (int)$id_berita,
                'tanggal'     => $tanggal,
                'jumlah_view' => 1
            ]);
        }
    }
}
