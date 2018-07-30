<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TrainingPost
 * @package App\Models
 * @property int id
 * @property string title
 * @property string body
 * @property int status
 * @property string video
 * @property datetime created_at
 * @property datetime updated_at
 */
class TrainingPost extends Model
{
    protected $fillable = ['title', 'body', 'status', 'video'];

    const STATUS_DRAFT = 10;
    const STATUS_PUBLISHED = 50;
    const STATUS_DELETED = 90;

    /**
     * @return array
     */
    public static function getStatuses() {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_DELETED => 'Deleted',
        ];
    }

    /**
     * @return string
     */
    public function getStatusStr() {
        $statuses = self::getStatuses();
        return $statuses[ $this->status ];
    }

    /**
     * @return TrainingPost[]
     */
    public static function findAllPublished() {
        return self::where('status', self::STATUS_PUBLISHED)->get();
    }
}
