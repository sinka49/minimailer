<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PageContent
 * @package App\Models
 * @property int id
 * @property string name
 * @property string content
 */
class PageContent extends Model
{
    protected $fillable = ['name','content'];
    protected $table = 'page_content';

    /**
     * @param string $name
     */
    public function setName( $name ) {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param array $content
     */
    public function setContent( $content ) {
        $this->content = serialize($content);
    }

    /**
     * @return string
     */
    public function getContent() {
        return unserialize($this->content);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public static function findByName( $name ) {
        return self::where('name', $name)->first();
    }


    /**
     * @param array $content
     */
    public static function saveHomePage( $content ) {
        $pc = self::findByName('HomePage');

        if( !$pc ){
            $pc = new self();
            $pc->setName('HomePage');
        }

        $pc->setContent($content);
        $pc->save();
    }

    /**
     * @return PageContent|mixed
     */
    public static function getHomePage() {
        $pc = self::findByName('HomePage');

        if( !$pc ){
            $pc = new self();
        }

        return $pc;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function __get( $key ) {
        if( isset($this->content) && !empty($this->content) ){
            $content = $this->getContent();
            if( $content && isset($content[$key]) ){
                return $content[$key];
            }
        }
        return parent::__get($key);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function __isset( $key ) {
        if( isset($this->content) && !empty($this->content) ){
            $content = $this->getContent();
            if( $content && isset($content[$key]) ){
                return true;
            }
        }
        return parent::__isset($key);
    }
}
