<?php

namespace App\Http\Controllers;

use App\Actions\Passport\IDAnalyzerOcrStrategy;
use App\Actions\Passport\ProgressPassport;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::all();

        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
            if ($request->input('submit') === 'process') {

                $ocrStrategy = new IDAnalyzerOcrStrategy;
                $progressPassport = new ProgressPassport($ocrStrategy);

                $result = $progressPassport->processImage($request->file('image'));
            }
        }

        Post::create([
            'title' => $request->title,
            'image' => $imagePath,
            'data' => $result && ! empty($result['raw_response']) ? $result['raw_response'] : null,
        ]);

        return redirect()->route('posts.index')->with('success', 'Post created successfully.');
    }

    public function show(Post $post)
    {
        return view('posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = $post->image;
        if ($request->hasFile('image')) {
            // Delete old image
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $imagePath = $request->file('image')->store('images', 'public');
        }

        $post->update([
            'title' => $request->title,
            'image' => $imagePath,
        ]);

        return redirect()->route('posts.index')->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post)
    {
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }
        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post deleted successfully.');
    }
    public function upload(Request $request)
    {

        // 1. استقبال الـ Base64 وتنظيفه
        $imageData = $request->input('image');
        $image = str_replace('data:image/jpeg;base64,', '', $imageData);
        $image = str_replace(' ', '+', $image);
        $imageBinary = base64_decode($image);

        // 2. إنشاء اسم ملف فريد
        $imageName = 'scan_' . time() . '.jpg';
        $imagePath = 'images/' . $imageName;

        // 3. تخزين الملف في الـ Public Disk
        \Storage::disk('public')->put($imagePath, $imageBinary);

        // 4. تشغيل منطق الـ OCR (نفس الكود بتاعك مع تعديل بسيط)
        $result = null;

        // محتاجين نبعت الملف للـ OCR. بما إن الـ OCR عندك بيتوقع ملف،
        // هنستخدم مسار الملف اللي لسه مخزنينه.
        $fullPath = storage_path('app/public/' . $imagePath);

//        try {
//            $ocrStrategy = new IDAnalyzerOcrStrategy;
//            $progressPassport = new ProgressPassport($ocrStrategy);
//
//            // ملاحظة: لو الـ processImage محتاج UploadedFile object،
//            // ممكن نمرر المسار أو نحول الـ binary لملف مؤقت.
//            $result = $progressPassport->processImage($fullPath);
//        } catch (\Exception $e) {
//            \Log::error("OCR Error: " . $e->getMessage());
//        }

        // 5. حفظ البيانات في الـ Database
        Post::create([
            'title' => 'Scanned Document ' . now()->format('Y-m-d H:i'),
            'image' => $imagePath,
//            'data' => ($result && !empty($result['raw_response'])) ? $result['raw_response'] : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Post Scanned successfully',
            'redirect_url' => route('posts.index')
        ]);
    }

}
