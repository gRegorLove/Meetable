<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Event, App\EventRevision;
use Illuminate\Support\Str;
use Auth;


class EventController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function new_event() {
        $event = new Event;
        return view('edit-event', [
            'event' => $event
        ]);
    }

    public function create_event() {
        $event = new Event();
        $event->name = request('name');

        $event->key = Str::random(12);
        $event->slug = preg_replace('/[^a-z]/', '-', strtolower($event->name));

        $event->location_name = request('location');
        $event->location_address = '';
        $event->location_locality = '';
        $event->location_region = '';
        $event->location_country = '';

        $event->start_date = date('Y-m-d', strtotime(request('start_date')));
        if(request('end_date'))
            $event->end_date = date('Y-m-d', strtotime(request('end_date')));
        if(request('start_time'))
            $event->start_time = date('H:i:00', strtotime(request('start_time')));
        if(request('end_time'))
            $event->end_time = date('H:i:00', strtotime(request('end_time')));

        $event->description = request('description');
        $event->website = request('website');

        $event->created_by = Auth::user()->id;
        $event->last_modified_by = Auth::user()->id;

        $event->save();

        return redirect($event->permalink());
    }

    public function edit_event(Event $event) {
        return view('edit-event', [
            'event' => $event
        ]);
    }

    public function save_event(Event $event) {
        $properties = [
            'name', 'start_date', 'end_date', 'start_time', 'end_time',
            'location_name', 'location_address', 'location_locality', 'location_region', 'location_country',
            'website', 'description'
        ];

        // Save a snapshot of the previous state
        $revision = new EventRevision;
        foreach(array_merge($properties, ['key','slug','created_by','last_modified_by']) as $p) {
            $revision->{$p} = $event->{$p} ?: '';
        }
        $revision->save();

        // Update the properties on the event
        foreach($properties as $p) {
            $event->{$p} = request($p) ?: '';
        }

        $event->last_modified_by = Auth::user()->id;
        $event->save();

        return redirect($event->permalink());
    }

}
