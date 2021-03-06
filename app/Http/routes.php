<?php

/*
  |--------------------------------------------------------------------------
  | Routes File
  |--------------------------------------------------------------------------
  |
  | Here is where you will register all of the routes in an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | This route group applies the "web" middleware group to every route
  | it contains. The "web" middleware group is defined in your HTTP
  | kernel and includes session state, CSRF protection, and more.
  |
 */

use App\User;
use App\Setting;
use App\Role;
use App\Feature;
use App\Package;
use App\Page;
use App\Menu;

Route::model('users', User::class);
Route::model('settings', Setting::class);
Route::model('roles', Role::class);
Route::model('packages', Package::class);
Route::model('features', Feature::class);
Route::model('pages', Page::class);
Route::model('menus', Menu::class);

Route::group(['middleware' => ['web']], function () {

    Route::get('/page/{slug}', 'FrontendController@staticPages');

    Route::get('/', 'FrontendController@index');

    Route::get('/pricing', 'FrontendController@pricing');

    Route::get('/components', 'FrontendController@components');

    Route::get('/contact-us', 'FrontendController@contactUs');

    Route::post('/contact-us', 'FrontendController@contactUsSubmit');

    Route::get('/blog', 'FrontendController@blog');

    Route::get('/blog/{slug}', 'FrontendController@post');

    Route::post('stripe/webhook', '\Laravel\Cashier\WebhookController@handleWebhook');

});

Route::group(['middleware' => 'web'], function () {
    /**
     * Authentication routes
     */
    Route::auth();
    /**
     * Admin routes
     */
    Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {

        Route::controllers([
            'datatables' => 'Admin\DatatablesController',
        ]);

        Route::get('/dashboard', 'Admin\DashboardController@index');

        Route::resource('users', 'Admin\UsersController');

        Route::get('settings/create/{type}', ['as' => 'admin.settings.create.type', 'uses' => 'Admin\SettingsController@createForm']);
        Route::get('settings/download/{settings}', ['as' => 'admin.settings.download', 'uses' => 'Admin\SettingsController@fileDownload']);
        Route::resource('settings', 'Admin\SettingsController');

        Route::resource('roles', 'Admin\RolesController');

        Route::resource('features', 'Admin\FeaturesController');

        Route::resource('packages', 'Admin\PackagesController');

        Route::resource('pages', 'Admin\PagesController');

        Route::resource('menus', 'Admin\MenusController');
    });

    /**
     * Member routes
     */
    Route::group(['prefix' => 'member'], function () {

        Route::controllers([
            'subscription' => 'SubscriptionController'
        ]);

        Route::get('/home', ['as' => 'member.home', 'uses' => 'MemberController@index']);
        Route::get('/pricing', ['as' => 'member.pricing', 'uses' => 'MemberController@pricing']);
        Route::get('/profile', ['as' => 'member.profile', 'uses' => 'MemberController@profile']);
        Route::get('/profile/edit', ['as' => 'member.profile.edit', 'uses' => 'MemberController@editProfile']);
        Route::put('/profile/edit', ['as' => 'member.profile.update', 'uses' => 'MemberController@updateProfile']);
    });
	
	Route::get('sitemap', function(){

		// create new sitemap object
		$sitemap = App::make("sitemap");

		// set cache key (string), duration in minutes (Carbon|Datetime|int), turn on/off (boolean)
		// by default cache is disabled
		$sitemap->setCache('laravel.sitemap', 1440);

		// check if there is cached sitemap and build new only if is not
		if (!$sitemap->isCached())
		{

			 
			 $posts = DB::table('pages')->orderBy('created_at', 'desc')->get();

			 // add every post to the sitemap
			 foreach ($posts as $post)
			 {
				if($post->blog_post ){
					$slug = "blog/".$post->slug;
				}else{
					$slug = "page/".$post->slug;
				}
				$sitemap->add(URL::to($slug), $post->updated_at,'0.9', 'daily');
			 }
		}

		return $sitemap->render('xml');

	}
	);
});
