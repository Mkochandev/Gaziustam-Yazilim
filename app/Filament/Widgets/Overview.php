<?php
namespace App\Filament\Widgets;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\{Gate,DB};
class Overview extends StatsOverviewWidget
{
    protected function getStats():array
    {
        $stats=[];
        if(Gate::allows('sales')) foreach(['new'=>'Yeni talepler','contact'=>'Görüşülen talepler'] as $s=>$label) $stats[]=Stat::make($label,DB::table('enquiries')->where('status',$s)->count());
        if(Gate::allows('content')){
            $stats[]=Stat::make('Taslak sürümler',DB::table('translations')->where('status','draft')->count());
            $missing=DB::table('contents')->crossJoin('locales')->whereNull('contents.deleted_at')->where('is_active',1)->whereNotExists(fn($q)=>$q->selectRaw('1')->from('translations')->whereColumn('content_id','contents.id')->whereColumn('locale','locales.code'))->count();
            $stats[]=Stat::make('Eksik dil sürümleri',$missing);
        }
        return $stats;
    }
}
