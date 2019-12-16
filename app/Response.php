<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    # https://laravel.com/docs/5.7/eloquent-mutators#array-and-json-casting
    protected $casts = [
        'photos' => 'array',
    ];

    public function event() {
        return $this->belongsTo('\App\Event');
    }

    public function author() {
        if($this->rsvp_user_id) {
            $user = User::where('id', $this->rsvp_user_id)->first();
            return [
                'name' => $user->name,
                'photo' => $user->photo,
                'url' => $user->url,
            ];
        } else {
            return [
                'name' => $this->author_name,
                'photo' => $this->author_photo,
                'url' => $this->author_url,
            ];
        }
    }

    // https://laravel.com/docs/5.7/eloquent-mutators
    // Ensure null values instead of empty strings

    public function setAuthorNameAttribute($value) {
        $this->attributes['author_name'] = $value ?: null;
    }

    public function setAuthorPhotoAttribute($value) {
        $this->attributes['author_photo'] = $value ?: null;
    }

    public function setAuthorUrlAttribute($value) {
        $this->attributes['author_url'] = $value ?: null;
    }

    public function setNameAttribute($value) {
        $this->attributes['name'] = $value ?: null;
    }

    public function setContentTextAttribute($value) {
        $this->attributes['content_text'] = $value ?: null;
    }

    public function setContentHTMLAttribute($value) {
        $this->attributes['content_html'] = $value ?: null;
    }

    public function setRsvpAttribute($value) {
        $this->attributes['rsvp'] = in_array(strtolower($value), ['yes','no','maybe','remote']) ?: null;
    }

}
