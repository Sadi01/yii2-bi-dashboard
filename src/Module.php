<?php

namespace sadi01\bidashboard;

/**
 *
 * ```php
 * 'modules' => [
 *     'bidashboard' => [
 *          'class' => 'sadi01\bidashboard\Module'
 *     ]
 * ]
 * ```
 *
 * @author Sadegh shafii <sadshafiei.01@gmail.com>
 * @since  1.0
 */
class Module extends \yii\base\Module
{
    /**
     * The module name
     */
    const MODULE = "bidashboard";
    public $layout="bid_main";

    public function __construct($id, $parent = null, $config = [])
    {
        parent::__construct($id, $parent, $config);
        if (array_key_exists('view_path', $this->params)) {
            $this->setViewPath($this->params['view_path']);
        }
    }
}