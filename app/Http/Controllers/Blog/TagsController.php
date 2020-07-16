<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;


use App\Category;
use App\Http\Requests\CreateTagRequest;
use App\Post;
use App\Tag;
use Illuminate\Http\Request;

class TagsController extends Controller
{
    public function index(Tag $tag )
    {
        return view('blog.tag')
            ->with('tag', $tag)
            ->with('categories', Category::all())
            ->with('tags', Tag::all())
            ->with('posts', $tag->posts()->searched()->simplePaginate(3));
    }

}
