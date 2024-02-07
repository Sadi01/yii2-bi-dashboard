<?php

namespace sadi01\bidashboard\models;

use Yii;
use yii\helpers\ArrayHelper;

class ReportBaseModel extends \yii\base\Model
{
    /**
     * Creates and populates a set of models.
     *
     * @param string $modelClass
     * @param array $multipleModels
     * @param array $data
     * @return array
     */
    public static function createMultiple($modelClass, $insertScenario = 'default', $updateScenario = 'default', $multipleModels = [], $data = null)
    {
        $model = new $modelClass(['scenario' => $insertScenario]);
        $formName = $model->formName();
        // added $data=null to function arguments
        // modified the following line to accept new argument
        $post = empty($data) ? Yii::$app->request->post($formName) : $data[$formName];
        $models = [];

        if (!empty($multipleModels)) {
            $keys = array_keys(ArrayHelper::map($multipleModels, 'id', 'id'));
            $multipleModels = array_combine($keys, $multipleModels);
        }

        if ($post && is_array($post)) {
            foreach ($post as $i => $item) {
                if (isset($item['id']) && !empty($item['id']) && isset($multipleModels[$item['id']])) {
                    $multipleModels[$item['id']]['scenario'] = $updateScenario;
                    $models[] = $multipleModels[$item['id']];
                } else {
                    $models[] = new $modelClass(['scenario' => $insertScenario]);
                }
            }
        }

        unset($model, $formName, $post);

        return $models;
    }
}
