<?php

// app/Http/Controllers/Api/NewsletterController.php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\NewsletterTopic;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function index(Request $r){
        $u = $r->user();
        $topics = NewsletterTopic::where('is_active', true)->orderBy('name')->get();

        $prefs = $topics->map(function($t) use ($u){
            $pivot = $u->newsletterTopics()->where('newsletter_topics.id',$t->id)->first()?->pivot;
            return [
                'id'    => $t->id,
                'slug'  => $t->slug,
                'name'  => $t->name,
                'subscribed' => $pivot ? (bool)$pivot->subscribed : false,
            ];
        });

        return response()->json(['items'=>$prefs]);
    }

    public function update(Request $r){
        $data = $r->validate([
            'topics'   => 'array',           // ex: ["actu","magazine"]
            'topics.*' => 'string'
        ]);
        $u = $r->user();

        $all = NewsletterTopic::whereIn('slug', $data['topics'] ?? [])->pluck('id','slug');
        // réinitialise (désabonne), puis abonne ceux passés
        $currentIds = NewsletterTopic::pluck('id')->all();
        foreach ($currentIds as $tid) {
            $u->newsletterTopics()->updateExistingPivot($tid, ['subscribed'=>false, 'unsubscribed_at'=>now()], false);
        }
        foreach ($all as $slug => $tid) {
            $u->newsletterTopics()->syncWithoutDetaching([
                $tid => ['subscribed'=>true, 'unsubscribed_at'=>null]
            ]);
        }
        return response()->json(['ok'=>true]);
    }
}
