<?php namespace Unisharp\Laravelfilemanager\controllers;

use Unisharp\Laravelfilemanager\controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Unisharp\Laravelfilemanager\httpclient\ImgDataHttpClient;

/**
 * Class LfmController
 * @package Unisharp\Laravelfilemanager\controllers
 */
class LfmController extends Controller {

    /**
     * @var
     */
    public $file_location = null;
    public $dir_location = null;
    public $file_type = null;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->file_type = Input::get('type', 'Images'); // default set to Images.

        if ('Images' === $this->file_type) {
            $this->dir_location = Config::get('lfm.images_url');
            $this->file_location = Config::get('lfm.images_dir');
        } elseif ('Files' === $this->file_type) {
            $this->dir_location = Config::get('lfm.files_url');
            $this->file_location = Config::get('lfm.files_dir');
        } else {
            throw new \Exception('unexpected type parameter');
        }

        $this->checkDefaultFolderExists('user');
        $this->checkDefaultFolderExists('share');
    }


    /**
     * Show the filemanager
     *
     * @return mixed
     */
    public function show()
    {
        $working_dir = '/';
        $working_dir .= (Config::get('lfm.allow_multi_user')) ? $this->getUserSlug() : Config::get('lfm.shared_folder_name');

        $data = ImgDataHttpClient::getCatMaps();
        $data_arr = json_decode(json_encode($data), true);

        return view('laravel-filemanager::index')
            ->with('working_dir', $working_dir)
            ->with('file_type', $this->file_type)
            ->with('category', $data_arr['category'])
            ->with('subcat', $data_arr['subcategory']);
    }


    /*****************************
     ***   Private Functions   ***
     *****************************/


    private function checkDefaultFolderExists($type = 'share')
    {
        if ($type === 'user' && \Config::get('lfm.allow_multi_user') !== true) {
            return;
        }

        $path = $this->getPath($type);

        if (!File::exists($path)) {
            File::makeDirectory($path, $mode = 0777, true, true);
        }
    }


    private function formatLocation($location, $type = null, $get_thumb = false)
    {
        if ($type === 'share') {
            return $location . Config::get('lfm.shared_folder_name');
        } elseif ($type === 'user') {
            return $location . $this->getUserSlug();
        }

        $working_dir = Input::get('working_dir');

        // remove first slash
        if (substr($working_dir, 0, 1) === '/') {
            $working_dir = substr($working_dir, 1);
        }


        $location .= $working_dir;

        if ($type === 'directory' || $type === 'thumb') {
            $location .= '/';
        }

        // Since we move thumb out of photo foder, user will not be inside thumbs folder now.

        if ($type === 'thumb') {
            $from = '/'.preg_quote(Config::get('lfm.images_url'), '/') . '/';
            $location = preg_replace($from, Config::get('lfm.images_thumb_url'), $location, 1);
        }
        return $location;
    }


    /****************************
     ***   Shared Functions   ***
     ****************************/


    public function getUserSlug()
    {
        return empty(auth()->user()) ? '' : \Auth::user()->user_field;
    }


    public function getPath($type = null, $get_thumb = false)
    {
        $path = base_path() . '/' . $this->file_location;

        $path = $this->formatLocation($path, $type);

        return $path;
    }


    public function getUrl($type = null)
    {
        $url = $this->dir_location;

        $url = $this->formatLocation($url, $type);
        $url = str_replace('\\','/',$url);

        return $url;
    }


    public function getDirectories($path)
    {
        $all_directories = File::directories($path);

        $arr_dir = [];
        foreach ($all_directories as $directory) {
            $dir_name = $this->getFileName($directory);
            $arr_dir[] = $dir_name;
        }
        return $arr_dir;
    }


    public function getFileName($file)
    {
        $lfm_dir_start = strpos($file, $this->file_location);
        $working_dir_start = $lfm_dir_start + strlen($this->file_location);
        $lfm_file_path = substr($file, $working_dir_start);

        $arr_dir = explode('/', $lfm_file_path);
        $arr_filename['short'] = end($arr_dir);
        $arr_filename['long'] = '/' . $lfm_file_path;

        return $arr_filename;
    }
}
