<?php

namespace App\Http\Controllers\API;

use App\Models\Word;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class WordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $words = Word::with(['categories'])
            ->get();
            return response()->json($words);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'term' => 'required|string|unique:words|max:255',
                'definition' => 'required|string',
                'user_id' => 'required|exists:users,id',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
    
            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $imagePath = uniqid() . '.' . request('image')->extension();
            request('image')->storeAs('public/images', $imagePath);
        
            $word = Word::create($request->all());
            if ($request->has('category_id')) {
            $word->categories()->attach($request->category_id);
            }
        
            return response()->json($word, 201);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Word $word)
    {
        try {
            return response()->json($word);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Word $word)
    {
        try {
            $validator = Validator::make($request->all(), [
                'term' => 'string|unique:words,term,'.$word->id.'|max:255',
                'definition' => 'string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
    
            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $fileName = null;

            if (request('image')) {
                $file = $request->file('image');
                $fileName = uniqid() . '.' . $file->extension();
                $file->storeAs('public/image/', $fileName);
            }
            
            $word= Word::findOrfail($word-> id);

            $word->update([
                'term' => $request->term,
                'definition' => $request->definition,
                'image' => $fileName ? $fileName : $word->image,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Word updated successfully'
            ], 200);
            
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    /**
 * Remove the specified resource from storage.
 */
public function destroy(Word $word)
{
    try {
        if ($word->image && Storage::disk('public')->exists($word->image)) {
            Storage::disk('public')->delete($word->image);
        }


        $word->delete();

        return response()->json(null, 204);
    } catch (\Throwable $th) {
        return response()->json(['message' => $th->getMessage()], 500);
    }
}
}
