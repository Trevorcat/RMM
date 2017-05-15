<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['web']], function () {

	Route::get('/apponload/',function(){
		return 'The API can not be used by the GET method';
	});
	Route::post('/apponload/','Controller\AppOnLoad@getAuthority');

	Route::get('/registerloaded/',function(){
		return 'The API can not be used by the GET method';
	});
	Route::post('/registerloaded/','Controller\RegisterLoaded@tunnelInfo');

	Route::get('/loginloaded/',function(){
		return 'The API can not be used by the GET method';
	});
	Route::post('/loginloaded/','Controller\RegisterLoaded@tunnelInfo');

	Route::get('/registerbuttonclick/',function(){
		return 'The API can not be used by the GET method';
	});
	Route::post('/registerbuttonclick/','Controller\RegisterButtonClick@authority');

	Route::get('/itemclick/',function(){
		return 'The API can not be used by the GET method';
	});
	Route::post('/itemclick/','Controller\ItemClick@returnDiseaseInfo');

	Route::get('/loginonload/',function(){
		return 'The API can not be used by the GET method';
	});
	Route::post('/loginonload/','Controller\LoginOnLoad@returnEventInfo');

	Route::get('/loginonloadtest/',function(){
		return 'The API can not be used by the GET method';
	});
	Route::post('/loginonloadtest/','Controller\LoginOnLoadTest@returnEventInfo');

	Route::get('/scanslided/',function(){
		return 'The API can not be used by the GET method';
	});
	Route::post('/scanslided/','Controller\ScanSlided@returnDisease');

	Route::get('/itemhold/',function(){
		return 'The API can not be used by the GET method';
	});
	Route::post('/itemhold/','Controller\ItemHold@returnDiseaseDetail');

	Route::get('/buttonokclick/',function(){
		return 'The API can not be used by the GET method';
	});
	Route::post('/buttonokclick/','Controller\ButtonOkClick@returnDiseases');

	Route::get('/buttonsearchclick/',function(){
		return 'The API can not be used by the GET method';
	});
	Route::post('/buttonsearchclick/','Controller\ButtonSearchClick@returnDiseases');

	Route::get('/upload/',function(){
		return 'The API can not be used by the GET method';
	});
	Route::post('/upload/','Controller\UpLoad@upload');

	Route::any('/test/','SearchDirTest@test');
	
});