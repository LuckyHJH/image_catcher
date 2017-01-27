<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
	private $_imgFolder = '';

	public function _initialize() {
		//IMG_FOLDER是存放图片的目录，自己去Conf/config.php里配置，填写绝对路径，最后包含“/”，如“/root/images/”
		$this->_imgFolder = C('IMG_FOLDER');
	}

	public function index(){
		import('Org.Util.simple_html_dom');
		try {
			$result = $this->_bingCatcher();
		} catch (\Exception $e) {
			$this->show($e->getMessage());
			exit;
		}
		$this->show(date('Y-m-d 星期w').'<br>'.$result['title'].'<br>'.
			'<img src="index.php/Home/Index/image?file='.$result['file'].'" width="100%"/>');
	}

	//网页直接显示图片
	public function image($file) {
		$image_info = getimagesize($file);
		if (empty(mime)) {$this->show('error');exit;}
		header("Content-type:".$image_info['mime']);
		echo file_get_contents($file);
	}

	private function _bingCatcher() {
		//抓取数据
		$url = 'http://cn.bing.com/';
		$Html = file_get_html( $url );
		if (empty($Html)) {
			throw new \Exception('网络未连接');
		}
		$html = $Html->find('html',0)->innertext;
		//preg_match('/g_img={.*?"(http.*?)".*?}/i', $html, $match);//20160905
		preg_match('/g_img={url.*?"(.*?)"/i', $html, $match);//20161221
		$image = $match[1];
		$title = $Html->find('#sh_cp',0)->getAttribute('title');
		$title = substr($title, 0, strpos($title, ' ('));
		if (empty($image) || empty($image)) {
			throw new \Exception('无法分析数据');
		}

		//保存文件
		$ext = substr($image, strrpos($image, '.'));
		$file = $this->_imgFolder.$title.$ext;
		if (!file_exists($file)) {
			if (!strstr($path,'://')) {
				$image = $url.$image;
			}
			$file_content = file_get_contents($image);
			if (empty($file_content)) {
				throw new \Exception('抓取图片失败');
			}
			if (!file_put_contents($file, $file_content)) {
				throw new \Exception('无法写入文件');
			}
		}
		return array('title'=>$title, 'file'=>$file);
	}
}
