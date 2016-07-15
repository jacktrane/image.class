<?php 

/**
* 图片相关类
* 压缩图片
* 图片水印
*/
class image
{
    private $image;
    private $info;

    /**
     * 读取图片内存信息
     * @param [type] $src 传入图片的路径名称
     */
    function __construct($src)
    {
        // $this->image = $src;
        $info = getimagesize($src);
        $this->info = array('width' => $info[0], 
            'height'=>$info[1],
            'type'  =>image_type_to_extension($info[2], false),
            'mime'  =>$info['mime']
            );
        /*将图片读入内存*/
        $fun = "imagecreatefrom{$imgType}";
        $this->image = $fun($src);
    }
    

    /**
     * 添加文字水印，要记得调用output输出图片
     * @param  [type] $size     图片尺寸调整
     * @param  int $angle    文字旋转角度
     * @param  array $local    文字坐标
     * @param  array $color    文字的rgba
     * @param  text $fontfile 字体文件路径
     * @param  text $content  文字内容
     * @return [type]           [description]
     */
    public function fontMark($size, $angle, $local, $color, $fontfile, $content)
    {   

        /*操作图片*/
        $imgCol = imagecolorallocatealpha($this->image, $color[0], $color[1], $color[2], $color[3]);
        // 图片位置
        imagettftext( $this->image, $size, $angle, $local['x'], $local['y'], $imgCol, $fonturl, $content);
        // imagecopymerge( src_im, dst_im, dst_x, dst_y, src_x, src_y, src_w, src_h, pct); 水印图片
    }

    /**
     * 图片水印
     * @param  text $markImage 作为水印的图片的路径
     * @param  array $local     作为水印的图片的坐标
     * @param  string $alpha     透明度
     * @param  array  $size      水印图片的大小
     * @return [type]            [description]
     */
    public function imageMark($markImage, $local, $alpha='30',$size=array('false'))
    {
        $markInfo = getimagesize($markImage);
        if ($size[0]) {
            $size = array('width' => $markInfo[0], 'height' => $markInfo[1]);
        }
        $markType = image_type_to_extension($markInfo[2], false);
        $creatImage = "imagecreatefrom{$markType}";
        $markImage = $creatImage($markImage);
        imagecopymerge($this->image, $imageMark, $local['x'], $local['y'], 0, 0, $size['width'], $size['height'], $alpha);
    }

    /**
     * 压缩图片
     * @param  [type] $imageSize 
     * 图片的尺寸 宽：$imageSize['width'] 
     *             高: $imageSize['height']
     * @return [type]            [description]
     */
    public function thumbImage($imageSize)
    {
        /*新建一个画布，将图片压缩到画布里面*/
        $imageThumb = imagecreatetruecolor( $imageSize['width'], $imageSize['height']);
        imagecopyresampled( $imageThumb, $this->image, 0, 0, 0, 0, $imageSize['width'], $imageSize['height'], $this->info['width'], $this->info['height']);
        imagedestroy($this->image);
        $this->image = $imageThumb;
    }

    /**
     * 输出图片
     * @return [type]
     */
    public function output()
    {   
        header("Content-type:".$this->info['mime']);
        $func = "image{$this->info['type']}";
        $func($this->image);
    }

    /**
     * 保存图片
     * @param  [type] $savePath 图片保存路径，不带后缀
     * @return [type]           [description]
     */ 
    public function saveImage($savePath)
    {
        $func = "image{$this->info['type']}";
        $func($this->image, $savePath.'.'.$this->info['type']);
    }

    /**
     * 销毁内存中图片
     */
    public function __destruct()
    {
        imagedestroy($this->image);
    }
}

?>