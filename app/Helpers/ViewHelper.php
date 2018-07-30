<?php

namespace App\Helpers;

use Illuminate\Support\Facades\URL;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Class ViewHelper
 * @package App\Helpers
 */
class ViewHelper {

    /**
     * @var \Illuminate\Http\Request
     */
    public $request;

    /**
     * NavHelper constructor.
     *
     * @param $request
     */
    public function __construct( $request ) {
        $this->request = $request;
    }


    /**
     * @return string
     */
    public function useCkeditor() {
        return $this->getScriptWrap( '//cdn.ckeditor.com/4.6.2/standard/ckeditor.js' );
    }

    /**
     * @param $url
     *
     * @return string
     */
    private function getScriptWrap( $url ) {
        return '<script src="' . $url . '"></script>';
    }

    /**
     * @param $content
     *
     * @return string
     */
    private function getScriptWrapContent( $content ) {
        return '<script>'.$content.'</script>';
    }

    /**
     * @param $name
     *
     * @return string
     */
    public function usePageScript( $pageKey = null ) {
//        $pageKey = $this->getPageKey();
        $pageScript = sprintf('/js/%s.js?%d', $pageKey, 2/*filemtime(Yii::getAlias("@frontend/web/js/{$pageKey}.js"))*/);

        return $this->getScriptWrap( URL::asset( $pageScript ) );
    }

    /**
     * @param      $var
     * @param null $model
     * @param bool $schema
     *
     * @return string|void
     */
    public function jsGate($var, $model = null, $schema = false) {
        if ($model === null && is_array($var)) {
            foreach ($var as $vvar => $model)
                $this->jsGate($vvar, $model);
            return;
        }

        if ($schema !== false){
            $model = static::toArray($model, $schema);
        }


        if (is_array($model) || is_object($model))
        {
            $model = json_encode(
                static::htmlEncode($model),
                JSON_UNESCAPED_UNICODE
            );
        }


        return $this->getScriptWrapContent( "$var = $model" );
    }

    public static function toArray($object, $properties = [], $recursive = true)
    {
        if (is_array($object)) {
            if ($recursive) {
                foreach ($object as $key => $value) {
                    if (is_array($value) || is_object($value)) {
                        $object[$key] = static::toArray($value, $properties, true);
                    }
                }
            }

            return $object;
        } elseif (is_object($object)) {
            if (!empty($properties)) {
                $className = get_class($object);
                if (!empty($properties[$className])) {
                    $result = [];
                    foreach ($properties[$className] as $key => $name) {
                        if (is_int($key)) {
                            $result[$name] = $object->$name;
                        } else {
                            $result[$key] = static::getValue($object, $name);
                        }
                    }

                    return $recursive ? static::toArray($result, $properties) : $result;
                }
            }
            if ($object instanceof Arrayable) {
                $result = $object->toArray([], [], $recursive);
            } else {
                $result = [];
                foreach ($object as $key => $value) {
                    $result[$key] = $value;
                }
            }

            return $recursive ? static::toArray($result) : $result;
        } else {
            return [$object];
        }
    }

    /**
     * Encodes special characters in an array of strings into HTML entities.
     * Only array values will be encoded by default.
     * If a value is an array, this method will also encode it recursively.
     * Only string values will be encoded.
     * @param array $data data to be encoded
     * @param boolean $valuesOnly whether to encode array values only. If false,
     * both the array keys and array values will be encoded.
     * @param string $charset the charset that the data is using. If not set,
     * [[\yii\base\Application::charset]] will be used.
     * @return array the encoded data
     * @see http://www.php.net/manual/en/function.htmlspecialchars.php
     */
    public static function htmlEncode($data, $valuesOnly = true, $charset = null)
    {
        if ($charset === null) {
//            $charset = Yii::$app->charset;
            $charset = 'UTF-8';
        }
        $d = [];
        foreach ($data as $key => $value) {
            if (!$valuesOnly && is_string($key)) {
                $key = htmlspecialchars($key, ENT_QUOTES, $charset);
            }
            if (is_string($value)) {
                $d[$key] = htmlspecialchars($value, ENT_QUOTES, $charset);
            } elseif (is_array($value)) {
                $d[$key] = static::htmlEncode($value, $valuesOnly, $charset);
            } else {
                $d[$key] = $value;
            }
        }

        return $d;
    }

    /**
     * Retrieves the value of an array element or object property with the given key or property name.
     * If the key does not exist in the array or object, the default value will be returned instead.
     *
     * The key may be specified in a dot format to retrieve the value of a sub-array or the property
     * of an embedded object. In particular, if the key is `x.y.z`, then the returned value would
     * be `$array['x']['y']['z']` or `$array->x->y->z` (if `$array` is an object). If `$array['x']`
     * or `$array->x` is neither an array nor an object, the default value will be returned.
     * Note that if the array already has an element `x.y.z`, then its value will be returned
     * instead of going through the sub-arrays. So it is better to be done specifying an array of key names
     * like `['x', 'y', 'z']`.
     *
     * Below are some usage examples,
     *
     * ~~~
     * // working with array
     * $username = \yii\helpers\ArrayHelper::getValue($_POST, 'username');
     * // working with object
     * $username = \yii\helpers\ArrayHelper::getValue($user, 'username');
     * // working with anonymous function
     * $fullName = \yii\helpers\ArrayHelper::getValue($user, function ($user, $defaultValue) {
     *     return $user->firstName . ' ' . $user->lastName;
     * });
     * // using dot format to retrieve the property of embedded object
     * $street = \yii\helpers\ArrayHelper::getValue($users, 'address.street');
     * // using an array of keys to retrieve the value
     * $value = \yii\helpers\ArrayHelper::getValue($versions, ['1.0', 'date']);
     * ~~~
     *
     * @param array|object $array array or object to extract value from
     * @param string|\Closure|array $key key name of the array element, an array of keys or property name of the object,
     * or an anonymous function returning the value. The anonymous function signature should be:
     * `function($array, $defaultValue)`.
     * The possibility to pass an array of keys is available since version 2.0.4.
     * @param mixed $default the default value to be returned if the specified array key does not exist. Not used when
     * getting value from an object.
     * @return mixed the value of the element if found, default value otherwise
     * @throws InvalidParamException if $array is neither an array nor an object.
     */
    public static function getValue($array, $key, $default = null)
    {
        if ($key instanceof \Closure) {
            return $key($array, $default);
        }

        if (is_array($key)) {
            $lastKey = array_pop($key);
            foreach ($key as $keyPart) {
                $array = static::getValue($array, $keyPart);
            }
            $key = $lastKey;
        }

        if (is_array($array) && array_key_exists($key, $array)) {
            return $array[$key];
        }

        if (($pos = strrpos($key, '.')) !== false) {
            $array = static::getValue($array, substr($key, 0, $pos), $default);
            $key = substr($key, $pos + 1);
        }

        if (is_object($array)) {
            return $array->$key;
        } elseif (is_array($array)) {
            return array_key_exists($key, $array) ? $array[$key] : $default;
        } else {
            return $default;
        }
    }

}