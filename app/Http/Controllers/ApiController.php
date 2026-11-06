<?php
namespace App\Http\Controllers;
use App\Models\Content;
use App\Services\{Publication,ContentInput};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController
{
    private function allow(Request $r,string $scope): void
    {
        abort_unless($r->user()->active && in_array($r->user()->role,['editor','owner'],true) && $r->user()->currentAccessToken() instanceof \Laravel\Sanctum\PersonalAccessToken && $r->user()->tokenCan('posts:'.$scope),403);
    }
    private function post($id): Content { return Content::whereIn('type',['article','news'])->findOrFail($id); }
    public function index(Request $r)
    {
        $this->allow($r,'read');
        return Content::with('translations')->whereIn('type',['article','news'])->orderByDesc('id')->paginate(20);
    }
    public function store(Request $r,?int $id=null)
    {
        $this->allow($r,'write');
        $data=$r->validate(['type'=>'required|in:news,article','source_locale'=>'required|exists:locales,code','category'=>'nullable|in:ai,software,apps,web,commerce,games','provider'=>'required_without:id|string|max:100','external_id'=>'required_without:id|string|max:180']);
        abort_if(empty($data['provider'])||empty($data['external_id']),422);
        if($id){
            $post=$this->post($id);
            abort_if($post->provider!==$data['provider']||$post->external_id!==$data['external_id'],409);
            $post->update($data);
        }else{
            $post=Content::firstOrCreate(['provider'=>$data['provider'],'external_id'=>$data['external_id']],$data);
            abort_if($post->type==='project',409);
            $post->update($data);
        }
        return response()->json($post,$post->wasRecentlyCreated?201:200);
    }
    public function translate(Request $r,int $id,string $locale,Publication $service)
    {
        $this->allow($r,'write');
        $post=$this->post($id);
        abort_unless(DB::table('locales')->where('code',$locale)->exists(),404);
        $data=ContentInput::translation($r);
        // Editing an already-published translation also changes live content.
        if(($data['status']??'draft')==='published' || $post->translations()->where(['locale'=>$locale,'status'=>'published'])->exists()) $this->allow($r,'publish');
        if(($data['status']??'draft')==='published') abort(422,'Use the publish endpoint.');
        $data['status']='draft'; $data['published_at']=null;
        return $service->save($id,$locale,$data);
    }
    public function publish(Request $r,int $id,string $locale)
    {
        $this->allow($r,'publish');
        $t=$this->post($id)->translations()->where('locale',$locale)->firstOrFail();
        abort_unless(trim(strip_tags($t->body??''))!=='',422);
        $data=$r->validate(['published_at'=>'nullable|date']);
        $t->update(['status'=>'published','published_at'=>$data['published_at']??now()]);
        return $t;
    }
}
