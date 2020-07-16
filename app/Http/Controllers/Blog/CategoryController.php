<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;


use App\Category;
use App\Http\Requests\CreateTagRequest;
use App\Post;
use App\Tag;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Category $category)
    {
//        $search = request()->query('search');
//
//        if ($search) {
//            $posts = $category->posts()->where('title', 'LIKE', "%{$search}%")->simplePaginate(3);
//        } else {
//            $posts = $category->posts()->simplePaginate(3);
//        }

        return view('blog.category')
            ->with('category', $category)
            ->with('posts', $category->posts()->searched()->simplePaginate(3))
            ->with('categories', Category::all())
            ->with('tags', Tag::all());
    }

}
