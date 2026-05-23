<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\TeacherPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherPostController extends Controller
{
    public function create()
    {
        return view('teacher.posts.create');
    }

    public function index()
    {
        // Use numeric sort direction for MongoDB driver (-1 for desc)
        $posts = TeacherPost::where('user_id', Auth::id())->orderBy('created_at', -1)->get();

        return view('teacher.posts.index', [
            'posts' => $posts,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:4000'],
            'category' => ['nullable', 'string', 'max:255'],
            'class_level' => ['nullable', 'string', 'max:100'],
            'experience' => ['nullable', 'numeric'],
            'budget' => ['nullable', 'numeric'],
            'tags' => ['nullable', 'string'],
            'online' => ['nullable', 'boolean'],
        ]);

        TeacherPost::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'body' => $request->body,
            'category' => $request->category,
            'class_level' => $request->class_level,
            'experience' => $request->experience,
            'budget' => $request->budget,
            'tags' => $request->tags ? explode(',', (string) $request->tags) : [],
            'online' => (bool) $request->boolean('online'),
            'is_featured' => false,
            'published_at' => now(),
        ]);

        return redirect()->route('teacher.dashboard')->with('success', 'Post published. Students will see it on their dashboards.');
    }

    public function destroy(TeacherPost $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        $post->delete();

        return back()->with('success', 'Post removed.');
    }

    // Show student requests associated with a post
    public function requests(TeacherPost $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        $requests = \App\Models\StudentRequest::where('post_id', $post->getKey())->orderBy('created_at', -1)->get();

        return view('teacher.posts.requests', compact('post', 'requests'));
    }
}
