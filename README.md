<p align="center">
    <a href="https://en.wikipedia.org/wiki/Business_intelligence" target="_blank" rel="external">
        <img src="https://raw.githubusercontent.com/Sadi01/yii2-bi-dashboard/master/src/img/yii.png" height="80px">
    </a>
    <a href="https://en.wikipedia.org/wiki/Business_intelligence" target="_blank" rel="external">
        <img src="https://raw.githubusercontent.com/Sadi01/yii2-bi-dashboard/master/src/img/BI.png" height="80px">
    </a>
    <h1 align="center">Business intelligence dashboard for Yii 2</h1>
    <br>
</p>

# Bidashboard

**Bidashboard** is a data visualization dashboard designed to provide insights into key metrics and data for business
intelligence purposes. It allows users to monitor and analyze various aspects of their business in real-time through
interactive charts and graphs.

This extension provides the Business Intelligence Dashboard for the [Yii framework 2.0](http://www.yiiframework.com).

For license information check the [LICENSE](LICENSE.md)-file.

Installation
------------

### Using Composer (Preferred Method):

The preferred way to install this extension is through [composer](http://getcomposer.org/download/):

```
composer require --prefer-dist sadi01/yii2-bi-dashboard:"*"
```

### Alternative Method:

If you prefer adding the bidashboard extension to your `composer.json` file manually, you can do so by adding the
following entry to the `require` section:

```json
{
  "require": {
    "sadi01/yii2-bi-dashboard": "*"
  }
}
```

After adding the entry, save the `composer.json` file and run the following command in the terminal or command prompt
within your project directory:

```
composer update
```

This command will fetch and install the bidashboard extension and its required dependencies into your Yii 2 project.

Configuration
-------------

To use this extension, you have to configure the bidashboard module in your application configuration:

```php
return [
    //....
    'modules' => [
        'bidashboard' => [
            'class' => 'sadi01\bidashboard\Module',
        ],
    ]
];
```




Env
-------------
You have to add the database configuration to env, its example is in - [Env.example](./src/env-config/.env.example)

DB Migrations
-------------

Run module migrations:

```php
php yii migrate --migrationPath=@sadi01/bidashboard/migrations
```

Or, Add migrations path in console application config:

```php
'controllerMap' => [
    'migrate' => [
        'class' => 'yii\console\controllers\MigrateController',
        'migrationNamespaces' => [],
        'migrationPath' => [
            '@vendor/sadi01/yii2-bi-dashboard/src/migrations',
            '@app/migrations'
        ]
    ],
],
```

How To Use
-------------
add to view model:

```php
use sadi01\bidashboard\widgets\ReportModalWidget;

<?= ReportModalWidget::widget([
    'queryParams' => $queryParams,
    'searchModel' => $searchModel,
    'searchModelMethod' => $searchWidget,
    'searchModelRunResultView' => $searchModelRunResultView,
    'searchRoute' => Yii::$app->request->pathInfo,
    'searchModelFormName' => $searchModelFormName,
    'outputColumn' => $outputColumn,
]) ?>
```

add to search model:

```php
public function searchWidget(array $params,int $rangeType,int $startRange,int $endRange)
{
    $query = Invoice::find();
    $dataProvider = new ActiveDataProvider([
        'query' => $query,
    ]);
    $this->load($params, '');
    $query->andFilterWhere(['between', 'created_at', $startRange, $endRange]);
    if ($rangeType == ReportWidget::RANGE_TYPE_MONTHLY) {
    ...
     }
    elseif ($rangeType == ReportWidget::RANGE_TYPE_DAILY) {
    ...    
    }
    return $dataProvider;
}
```
Advanced config
-------------
- [Installation Guide](./src/guide/installation.md)

- [Description Guide](./src/guide/description.md)

- [Usage Guide](./src/guide/usage.md)

- [Access Control Guide (RBAC)](./src/guide/rbac.md)