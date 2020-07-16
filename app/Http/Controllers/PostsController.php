<?php

namespace App\Http\Controllers;
use App\Category;
use App\Http\Requests\UpdatePostRequest;
use App\Tag;
use Illuminate\Http\Request;
use App\Http\Requests\CreatePostsRequest;
use App\Post;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Input\Input;


class PostsController extends Controller
{

    public function __construct()
    {
        $this->middleware('verifyCategoriesCount')->only(['create', 'store']);
        $this->uploadPath = public_path('uploads/posts');

    }


    public function index()
    {
        return view('posts.index')->with('posts', Post::all());

    }


    public function create()
    {
        return view('posts.create')->with('categories', Category::all())->with('tags', Tag::all());
        
    }


    public function store(CreatePostsRequest $request)
    {

        // upload the image to storage
        $image = $request->image->store('posts');

        $image = $request->file('image');
        $fileName = $image->getClientOriginalName();
        $destination = $this->uploadPath;
        $successUpload = $image->move($destination, $fileName);

//        if ($successUpload) {
//
//            $extension = $image->getClientOriginalExtension();
//            $thumbnail = str_replace("{$extension}", "thumb_.{$extension}", $fileName);
//
//            Image::make($destination . '/' . $fileName)
//                ->resize($with, $height)
//                ->save($destination . '/' . $thumbnail);
//
//        }
//        $data['image'] = $fileName;
//
//

        // create the post
        $post =Post::create([
            'title' => $request->title,
            'description' => $request->description,
            'content' => $request->body,
            'image' => $fileName,
            'published_at' => $request->published_at,
            'category_id' => $request->category,
            'user_id' => auth()->user()->id
        ]);

        if ($request->tags) {
            $post->tags()->attach($request->tags);
        }
        // flash message
        session()->flash('success', 'Post created successfully.');
        // redirect user

        return redirect(route('posts.index'));
    }


    public function show($id)
    {
        //
    }

    public function edit(Post $post)
    {
        //
        return view('posts.create')->with('post', $post)->with('categories', Category::all())->with('tags', Tag::all());


    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $data = $request->only(['title', 'description', 'published_at', 'content']);
        // check if new image
        if ($request->hasFile('image')) {
            // uplload it
            $image = $request->image->store('posts');
            // delete old one
            $post->deleteImage();

            $data['image'] = $image;
        }

        if ($request->tags) {
            $post->tags()->sync($request->tags);
        }

        // update attributes
        $post->update($data);

        // flash message
        session()->flash('success', 'Post updated successfully.');

        // redirect user
        return redirect(route('posts.index'));
    }


    public function destroy($id)
    {

        $post = Post::withTrashed()->where('id', $id)->firstOrFail();

        if ($post->trashed()) {
            $post->deleteImage();
            $post->forceDelete();
        } else {
            $post->delete();
        }
        session()->flash('success', 'Post trashed successfully.');

        return redirect(route('posts.index'));
    }

    public function trashed()
    {
        $trashed = Post::onlyTrashed()->get();

        return view('posts.index')->with('posts', $trashed);
    }

    public function restore($id)
    {
        $post = Post::withTrashed()->where('id', $id)->firstOrFail();

        $post->restore();

        session()->flash('success', 'Post restored successfully.');

        return redirect()->back();
    }

}
