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

	Route::any('/apponload/','Controller\AppOnLoad@getAuthority');

	Route::any('/registerloaded/','Controller\RegisterLoaded@tunnelInfo');

	Route::any('/loginloaded/','Controller\RegisterLoaded@tunnelInfo');

	Route::any('/registerbuttonclick/','Controller\RegisterButtonClick@authority');

	Route::any('/itemclick/','Controller\ItemClick@returnDiseaseInfo');

	Route::any('/loginonload/','Controller\LoginOnLoad@returnEventInfo');

	Route::any('/scanslided/','Controller\ScanSlided@returnDisease');

	Route::any('/itemhold/','Controller\ItemHold@returnDiseaseDetail');

	Route::any('/buttonokclick/','Controller\ButtonOkClick@returnDiseases');

	Route::any('/test/','SearchDirTest@test');
	
});