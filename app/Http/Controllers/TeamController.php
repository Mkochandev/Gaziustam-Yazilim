<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\{DB,Storage};
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class TeamController
{
    public const STATES=['new'=>'Yeni','review'=>'İnceleniyor','contact'=>'Görüşülüyor','offered'=>'Teklif gönderildi','accepted'=>'Kabul edildi','closed'=>'Kapatıldı','spam'=>'Spam'];
    public function index(Request $r)
    {
        $query=DB::table('enquiries')->latest('submitted_at');
        foreach(['status','service_code','locale','assigned_to'] as $field) if($r->filled($field)) $query->where($field,$r->query($field));
        foreach(['from'=>'>=','to'=>'<='] as $field=>$op) if($r->filled($field)) $query->whereDate('submitted_at',$op,$r->query($field));
        if($r->filled('q')) $query->where(function($q)use($r){ foreach(['contact_name','contact_email','public_reference'] as $f) $q->orWhere($f,'like','%'.$r->query('q').'%'); });
        return view('admin.enquiries',['items'=>$query->paginate(30)->withQueryString(),'states'=>self::STATES,'team'=>$this->team()]);
    }
    private function team(){ return User::where('active',true)->whereIn('role',['sales','owner'])->orderBy('name')->get(); }
    public function show(int $id)
    {
        $item=DB::table('enquiries')->find($id); abort_unless($item,404);
        return view('admin.enquiry',['item'=>$item,'states'=>self::STATES,'team'=>$this->team(),'answers'=>DB::table('enquiry_answers')->where('enquiry_id',$id)->get(),'events'=>DB::table('enquiry_events')->where('enquiry_id',$id)->orderBy('id')->get(),'attachments'=>DB::table('enquiry_attachments')->where('enquiry_id',$id)->get()]);
    }
    public function update(Request $r,int $id)
    {
        $data=$r->validate(['status'=>['required',Rule::in(array_keys(self::STATES))],'assigned_to'=>['nullable',Rule::in($this->team()->pluck('id'))],'note'=>'nullable|string|max:5000']);
        DB::transaction(function() use($r,$id,$data){
            $item=DB::table('enquiries')->where('id',$id)->lockForUpdate()->firstOrFail();
            DB::table('enquiries')->where('id',$id)->update(['status'=>$data['status'],'assigned_to'=>$data['assigned_to']??null]);
            DB::table('enquiry_events')->insert(['enquiry_id'=>$id,'user_id'=>$r->user()->id,'old_status'=>$item->status,'new_status'=>$data['status'],'note'=>$data['note']??null,'created_at'=>now()]);
        });
        return back()->with('notice','Talep güncellendi.');
    }
    public function download(int $id,int $attachment)
    {
        $file=DB::table('enquiry_attachments')->where(['enquiry_id'=>$id,'id'=>$attachment])->firstOrFail();
        return Storage::disk('local')->download($file->path,$file->original_name,['Content-Type'=>'application/octet-stream','X-Content-Type-Options'=>'nosniff','Cache-Control'=>'private, no-store']);
    }
    public function access()
    {
        return view('admin.access',['users'=>User::orderBy('name')->get(),'tokens'=>DB::table('personal_access_tokens')->latest('id')->get()]);
    }
    public function user(Request $r)
    {
        $data=$r->validate(['name'=>'required|string|max:255','email'=>'required|email|max:255|unique:users','password'=>'required|string|min:12|max:100','role'=>'required|in:owner,sales,editor']);
        $user=new User($data); $user->role=$data['role']; $user->save();
        return back()->with('notice','Kullanıcı oluşturuldu.');
    }
    public function token(Request $r)
    {
        $data=$r->validate(['name'=>'required|string|max:100','abilities'=>'required|array|min:1','abilities.*'=>['required',Rule::in(['posts:read','posts:write','posts:publish'])]]);
        $token=$r->user()->createToken($data['name'],$data['abilities']);
        return back()->with('token',$token->plainTextToken);
    }
    public function revoke(int $id){ DB::table('personal_access_tokens')->where('id',$id)->delete(); return back()->with('notice','Token iptal edildi.'); }
}
