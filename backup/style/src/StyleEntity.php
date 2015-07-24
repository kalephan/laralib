<?php
namespace Kalephan\Style;

use Kalephan\LKS\EntityAbstract;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic as Image;

class StyleEntity extends EntityAbstract
{

    function __config()
    {
        return array(
            '#id' => 'style',
            '#name' => 'styles',
            '#class' => '\Kalephan\Style\StyleEntity',
            '#title' => lks_lang('Style'),
            '#fields' => array(
                'style' => array(
                    '#name' => 'style',
                    /*'#title' => lks_lang('Tên kiểu'),
                    '#type' => 'text',
                    '#required' => true,
                    '#validate' => 'required|max:64',*/
                ),
                'width' => array(
                    '#name' => 'width',
                    /*'#title' => lks_lang('Chiều rộng'),
                    '#type' => 'text',
                    '#validate' => 'numeric|between:0,2000',*/
                ),
                'height' => array(
                    '#name' => 'height',
                    /*'#title' => lks_lang('Chiều cao'),
                    '#type' => 'text',
                    '#validate' => 'numeric|between:0,2000',*/
                ),
                'type' => array(
                    '#name' => 'type',
                    /*'#title' => lks_lang('Kiểu'),
                    '#type' => 'select',
                    '#options' => array(
                        'scale-and-crop' => lks_lang('Scale and crop'),
                        'scale' => lks_lang('Scale'),
                    ),
                    '#required' => true,
                    '#validate' => 'required',*/
                ),
                'is_upsize' => array(
                    '#name' => 'is_upsize',
                    /*'#title' => lks_lang('Is Upsize'),
                    '#type' => 'radios',
                    '#options' => array(
                        0 => lks_lang('Có'),
                        1 => lks_lang('Không'),
                    ),
                    '#validate' => 'numeric|between:0,1',
                    '#default' => 0,*/
                )
            ),
            '#indelibility' => array(
                1,
                2
            )
        );
    }

    public function image($file_original, $style = 'normal')
    {
        $style = $this->loadEntity($style);
        
        if (! isset($style->style)) {
            $img = Image::make(public_path() . $file_original);
            
            $image = array(
                'path' => $file_original,
                'width' => $img->width(),
                'height' => $img->height()
            );
        } else {
            $path_file = config('lks.file path', '/files');
            $path_style = "$path_file/styles/$style";
            $file_style = "$path_style/" . str_replace("$path_file//f14/images/", '', $file_original);
            
            if (! file_exists(public_path() . $file_style)) {
                $this->_createDirectory(public_path() . $file_style);
                $img = Image::make(public_path() . $file_original);
                
                switch ($style->type) {
                    case 'scale-and-crop':
                        $img->resize($style->width, $style->height, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $img->crop($style->width, $style->height);
                        
                        $img->fill(config('lks.file image background', '#ffffff'), 0, 0);
                        
                        break;
                    
                    case 'scale':
                        $style->width = ! empty($style->width) ? $style->width : null;
                        $style->height = ! empty($style->height) ? $style->height : null;
                        
                        if (! empty($style->is_upsize)) {
                            $img->resize($style->width, $style->height, function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            });
                        } else {
                            $img->resize($style->width, $style->height, function ($constraint) {
                                $constraint->aspectRatio();
                            });
                        }
                        
                        break;
                }
                
                $img->save(public_path() . $file_style, config('lks.file image quality', 70));
            } else {
                $img = Image::make(public_path() . $file_style);
            }
            
            $image = array(
                'path' => $file_style,
                'width' => $img->width(),
                'height' => $img->height()
            );
        }
        
        return $image;
    }

    private function _createDirectory($path_style)
    {
        // remove file name
        $path_style = substr($path_style, 0, strrpos($path_style, '/'));
        
        if (File::isDirectory($path_style)) {
            return;
        }
        
        $path_style = explode('/', $path_style);
        $path = array_shift($path_style);
        if (count($path_style)) {
            foreach ($path_style as $path_sub) {
                $path .= "/$path_sub";
                if (! File::isDirectory($path)) {
                    File::makeDirectory($path);
                }
            }
        }
    }
}