<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

/*
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass()`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 */
/** @var \Cake\Routing\RouteBuilder $builder */
$routes->setRouteClass(DashedRoute::class);

$routes->scope('/api', function (RouteBuilder $builder) {

    $builder->connect('/user/current', ['controller' => 'User', 'action' => 'getCurrentUser']);
    $builder->connect('/user/:userID/edit', ['controller' => 'User', 'action' => 'editUser', '_method' => 'POST']);    
    $builder->connect('/user/:userID/status/:newStatus', ['controller' => 'User', 'action' => 'changeUserStatus', '_method' => 'POST']);
    $builder->connect('/user/:userID/role/:newRole', ['controller' => 'User', 'action' => 'changeUserRole', '_method' => 'POST']);
    $builder->connect('/user/confirmationresend', ['controller' => 'User', 'action' => 'confirmationResend', '_method' => 'POST']);    
    
    $builder->connect('/data/:table/:scope', ['controller' => 'Data', 'action' => 'getUserData', '_method' => 'GET']);
    
    $builder->connect('/user/data/:table', ['controller' => 'Data', 'action' => 'getUserData', '_method' => 'GET']);
    $builder->connect('/user/data/:table/consolidate', ['controller' => 'Data', 'action' => 'consolidateUserData', '_method' => 'POST']);
    $builder->connect('/user/data/:table/exclude', ['controller' => 'Data', 'action' => 'excludeUserData', '_method' => 'POST']);
    $builder->connect('/user/data/:table/alias', ['controller' => 'Data', 'action' => 'updateAliasValue', '_method' => 'POST']);
    $builder->connect('/user/data/:table/revert-alias', ['controller' => 'Data', 'action' => 'revertAlias', '_method' => 'POST']);
    
    $builder->connect('/directory/:table', ['controller' => 'Data', 'action' => 'getDirectoryData', '_method' => 'GET']);
    
    $builder->connect('/user/project/:projectID/publish', ['controller' => 'Data', 'action' => 'publishUserData', '_method' => 'POST']);    
    
    $builder->connect('/project/private', ['controller' => 'Data', 'action' => 'getPrivateProjects', '_method' => 'GET']);
    
    $builder->connect('/resource/all', ['controller' => 'Resource', 'action' => 'listResources']);
    $builder->connect('/resource/', ['controller' => 'Resource', 'action' => 'uploadResource', '_method' => 'POST']);
    $builder->connect('/resource/:resourceID', ['controller' => 'Resource', 'action' => 'uploadResource', '_method' => 'POST']);
    $builder->connect('/resource/:resourceID/delete', ['controller' => 'Resource', 'action' => 'deleteResource', '_method' => 'POST']);
    $builder->connect('/resource/:resourceID/metadata', ['controller' => 'Resource', 'action' => 'updateResource', '_method' => 'POST']);
    $builder->connect('/resource/file/:filename', ['controller' => 'Resource', 'action' => 'readObject']);
    
    $builder->connect('/reports/:chartType/:groupBy', ['controller' => 'Report', 'action' => 'getReportResults']);
    
    $builder->connect('/feed', ['controller' => 'Feed', 'action' => 'getFeed']);
    
    $builder->connect('/setting/all', ['controller' => 'Setting', 'action' => 'listSettings']);
    $builder->connect('/setting/', ['controller' => 'Setting', 'action' => 'updateSetting', '_method' => 'POST']);
    $builder->connect('/setting/:settingID', ['controller' => 'Setting', 'action' => 'updateSetting', '_method' => 'POST']);
    $builder->connect('/setting/:settingID/delete', ['controller' => 'Setting', 'action' => 'deleteSetting', '_method' => 'POST']);

    $builder->connect('/setting/listlogs', ['controller' => 'Setting', 'action' => 'listlogs']);
    $builder->connect('/setting/:logName/clearLogs', ['controller' => 'Setting', 'action' => 'clearLogs','_method' => 'POST']);
    //$builder->connect('/setting/:logName/downloadLogs', ['controller' => 'Setting', 'action' => 'downloadLogs','_method' => 'POST']);
    
    $builder->connect('/file/import', ['controller' => 'Upload', 'action' => 'importUploadData', '_method' => 'POST']);
    $builder->connect('/file/export/all/sqlite', ['controller' => 'Download', 'action' => 'exportAllSQL', '_method' => 'GET']);
    $builder->connect('/file/export/all/csv', ['controller' => 'Download', 'action' => 'exportAllCSV', '_method' => 'GET']);
    $builder->connect('/file/export/users/csv', ['controller' => 'Download', 'action' => 'exportUserCSV', '_method' => 'GET']);
    $builder->connect('/file/export/key/csv/:exportType', ['controller' => 'Download', 'action' => 'exportAllKeyCSV', '_method' => 'GET']);
    $builder->connect('/file/export/data', ['controller' => 'Download', 'action' => 'exportData', '_method' => 'GET']);
    
    $builder->setExtensions(['json']);
    $builder->resources('User');
    $builder->resources('Token');
});

$routes->scope('/api/system', function (RouteBuilder $builder) {
    $builder->connect('/country/all', ['controller' => 'System', 'action' => 'showCountryList']);
    $builder->connect('/country_major_region/all', ['controller' => 'System', 'action' => 'showCountryMajorRegionList']);
    $builder->connect('/world_region/all', ['controller' => 'System', 'action' => 'showWorldRegionList']);    
    $builder->connect('/gender/all', ['controller' => 'System', 'action' => 'showGenderList']);
    $builder->connect('/salutation/all', ['controller' => 'System', 'action' => 'showSalutationList']);
    $builder->connect('/approval_status/all', ['controller' => 'System', 'action' => 'showSystemApprovalStatusList']);
});

$routes->scope('/api/admin', function (RouteBuilder $builder) {
    $builder->connect('/users', ['controller' => 'Admin', 'action' => 'getUsers', '_method' => 'GET']);
});

$routes->scope('/', function (RouteBuilder $builder) {
    /**
     * Here, we are connecting '/' (base path) to a controller called 'Pages',
     * its action called 'display', and we pass a param to select the view file
     * to use (in this case, src/Template/Pages/home.ctp)...
     */
    $builder->connect('/', ['controller' => 'Pages', 'action' => 'display', 'home']);
   // $builder->connect('/', ['controller' => 'Pages', 'action' => 'display', 'homenew']);
    /**
     * ...and connect the rest of 'Pages' controller's URLs.
     */
    $builder->connect('/pages/*', ['controller' => 'Pages', 'action' => 'display']);
    $builder->connect('/dashboard/', ['controller' => 'Pages', 'action' => 'dashboard']);
    $builder->connect('/uploads/', ['controller' => 'Upload', 'action' => 'index']);
    $builder->connect('/downloads/', ['controller' => 'Download', 'action' => 'index']);
    $builder->connect('/reports/', ['controller' => 'Report', 'action' => 'index']);
    $builder->connect('/help/', ['controller' => 'Pages', 'action' => 'help']);
    $builder->connect('/help/usage', ['controller' => 'Pages', 'action' => 'helpUsage']);
    $builder->connect('/profile/', ['controller' => 'User', 'action' => 'profile']);
    $builder->connect('/timeout/', ['controller' => 'User', 'action' => 'timeout']);

    $builder->connect('/about/', ['controller' => 'Pages', 'action' => 'aboutfeast']);
    $builder->connect('/news/', ['controller' => 'Pages', 'action' => 'feastnews']);
    $builder->connect('/signin/', ['controller' => 'Pages', 'action' => 'loginfeast']);
    $builder->connect('/signup/', ['controller' => 'Pages', 'action' => 'registerfeast']);


    /**
     * Connect catchall routes for all controllers.
     *
     * Using the argument `InflectedRoute`, the `fallbacks` method is a shortcut for
     *    `$builder->connect('/:controller', ['action' => 'index'], ['routeClass' => 'InflectedRoute']);`
     *    `$builder->connect('/:controller/:action/*', [], ['routeClass' => 'InflectedRoute']);`
     *
     * Any route class can be used with this method, such as:
     * - DashedRoute
     * - InflectedRoute
     * - Route
     * - Or your own route class
     *
     * You can remove these routes once you've connected the
     * routes you want in your application.
     */
    $builder->fallbacks('InflectedRoute');
});

/**
 * Load all plugin routes.  See the Plugin documentation on
 * how to customize the loading of plugin routes.
 */
//Plugin::routes();
