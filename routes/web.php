<?php
use App\Http\Controllers\{SiteController,EnquiryController,EditorController,TeamController};
use App\Http\Middleware\Locale;
use Illuminate\Support\Facades\Route;

Route::redirect('/','/de');
Route::get('/sitemap.xml',[SiteController::class,'sitemap']);
Route::prefix('admin')->middleware(['auth','cache.headers:no_store;private'])->group(function(){
    Route::middleware('can:content')->group(function(){
        Route::get('yazilar/{content}/{locale}',[EditorController::class,'edit']);
        Route::post('yazilar/{content}/{locale}',[EditorController::class,'save']);
        Route::get('onizleme/{translation}',[EditorController::class,'preview'])->middleware('signed')->name('studio.preview');
    });
    Route::middleware('can:manage')->group(function(){
        Route::get('form',[EditorController::class,'form']); Route::post('form',[EditorController::class,'draft']);
        Route::post('form/{version}/publish',[EditorController::class,'publish']);
        Route::get('erisim',[TeamController::class,'access']); Route::post('users',[TeamController::class,'user']);
        Route::post('tokens',[TeamController::class,'token']); Route::delete('tokens/{id}',[TeamController::class,'revoke']);
    });
    Route::middleware('can:sales')->group(function(){
        Route::get('talepler',[TeamController::class,'index']); Route::get('talepler/{id}',[TeamController::class,'show']);
        Route::post('talepler/{id}',[TeamController::class,'update']); Route::get('talepler/{id}/ek/{attachment}',[TeamController::class,'download']);
    });
});
Route::prefix('{locale}')->middleware(Locale::class)->group(function(){
    Route::get('/',[SiteController::class,'home']);
    Route::get('/{page}',fn(string $locale,string $page)=>view('legal',compact('page')))->whereIn('page',['legal','privacy']);
    Route::get('/{section}',[SiteController::class,'index'])->whereIn('section',['blog','projects']);
    Route::get('/{section}/{slug}',[SiteController::class,'show'])->whereIn('section',['blog','projects']);
    Route::post('/enquiries',[EnquiryController::class,'store'])->middleware('throttle:10,1');
});
