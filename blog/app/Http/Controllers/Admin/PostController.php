<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use DB;
use Intervention\Image\ImageManagerStatic ;
use Illuminate\Support\Facades\File;
use LaravelVideoEmbed;
use Cohensive\Embed\Facades\Embed;



class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::with(['user', 'category', 'tags', 'comments'])->paginate(10);
        
        return view('admin.posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::pluck('name', 'id')->all();
        $tags = Tag::pluck('name', 'name')->all();

        return view('admin.posts.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {   
            

      
        if($request->hasFile('image')){

             $fileName =  "image_".rand(0, 999999).time().'.'.$request->image->getClientOriginalExtension();
            // // $request->image->storeAs('Posts_Images', $fileName);
            // // $request->image->storeAs('images', $imageName, 's3')
            //  $request->image->move(public_path('post_img'), $fileName);
            $image_resize = Image::make($request->file('image')->getRealPath())->resize(960, 640);              
            $image_resize->save(public_path('\post_img') ."\\".$fileName);
             $post = Post::create([
                'title'       => $request->title,
                'body'        => $request->body,
                'category_id' => $request->category_id,
                 'image'        =>$fileName,
                'post_type'     => 'image'
                
                
            ]);
  
        }
        else{
              if(isset($url))   {
            $url =$request->video_url;
            
            if ( strstr( $url, 'dailymotion' ) ) {
                $embed = $url;
            } 
            elseif( strstr( $url, 'vimeo' ) ) {
           
                 $url =$request->video_url;
                 $id = preg_replace("/[^0-9]/", '', $url);
                 $embed = "https://player.vimeo.com/video/$id";
                //  $whitelist = ['YouTube', 'Vimeo','dailymotion',];
                // $embed = LaravelVideoEmbed::parse($url, $whitelist);  
            }
            elseif( strstr( $url, 'youtube' ) ){
                   
                $id = substr($url, strpos($url, "=") + 1 );
                $embed = "https://www.youtube.com/embed/$id";


            }
            $post = Post::create([
                'title'       => $request->title,
                'body'        => $request->body,
                'category_id' => $request->category_id,
                'video_url'    =>$embed,
                'post_type'    =>'video'
                
            ]);
              }
              $post = Post::create([
                'title'       => $request->title,
                'body'        => $request->body,
                'category_id' => $request->category_id,
                 'post_type'    => 'content'
                
            ]);
        }
        $tagsId = collect($request->tags)->map(function ($tag) {
            return Tag::firstOrCreate(['name' => $tag])->id;
        });

        $post->tags()->attach($tagsId);
        flash()->overlay('Post created successfully and as soon as it will be publish');
       
        // return redirect('/admin/posts');
        return redirect('home');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        $post = $post->load(['user', 'category', 'tags', 'comments']);
            
        return view('admin.posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        if ($post->user_id != auth()->user()->id && auth()->user()->is_admin == false) {
            flash()->overlay("You can't edit other peoples post.");

            return redirect('/admin/posts');
        }

        $categories = Category::pluck('name', 'id')->all();
        $tags = Tag::pluck('name', 'name')->all();

        return view('admin.posts.edit', compact('post', 'categories', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PostRequest $request, Post $post)
    {
        // disbust cache
        Cache::forget($post->etag);

        $post->update([
            'title'       => $request->title,
            'body'        => $request->body,
            'category_id' => $request->category_id,
        ]);

        $tagsId = collect($request->tags)->map(function ($tag) {
            return Tag::firstOrCreate(['name' => $tag])->id;
        });

        $post->tags()->sync($tagsId);
        flash()->overlay('Post updated successfully.');

        return redirect('/admin/posts');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        if ($post->user_id != auth()->user()->id && auth()->user()->is_admin == false) {
            flash()->overlay("You can't delete other peoples post.");

            return redirect('/admin/posts');
        }

        $post->delete();
        flash()->overlay('Post deleted successfully.');

        return redirect('/admin/posts');
    }

    public function publish(Post $post)
    {
        $post->is_published = ! $post->is_published;
        $post->save();
        flash()->overlay('Post changed successfully.');

        return redirect('/admin/posts');
    }
}
