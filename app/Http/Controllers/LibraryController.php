<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LibraryController extends Controller
{

    public function index(Request $request)
    {
        return view('library.index');
    }

    public function create(Request $request)
    {
        $folder = new Folder();
        $folder->parentId = $request->input('parent_id');
        $folder->folderName = $request->input('folder_name');
        $folder->save();

        return response()->json($folder);
    }

    public function getFolders()
    {
        $folders = Folder::all();

        return response()->json($folders);
    }

    public function destroy(Request $request)
    {
        $id = $request->id;

        $this->deleteFolderAndSubfolders($id);

        return response()->json(['message' => 'Folder and its contents deleted successfully']);
    }

    protected function deleteFolderAndSubfolders($folderId)
    {
        $folder = Folder::find($folderId);

        if ($folder) {
            $subfolders = Folder::where('parentId', $folderId)->get();
            foreach ($subfolders as $subfolder) {
                $this->deleteFolderAndSubfolders($subfolder->id);
            }

            Image::where('parentId', $folderId)->delete();

            foreach ($subfolders as $subfolder) {
                $this->deleteFolderAndSubfolders($subfolder->id);
            }

            $folder->delete();
        }
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $parentId = $request->input('parentId');

            // Use the original filename
            $filename = $image->getClientOriginalName();

            $path = $image->storeAs('public/images', $filename);

            Image::create([
                'fileName' => $filename,
                'parentId' => $parentId,
                'path' => $path,
            ]);

            return response()->json(['success' => true, 'filename' => $filename]);
        }

        return response()->json(['success' => false, 'message' => 'No image uploaded']);
    }

    public function getImages(Request $request)
    {
        $parentId = $request->input('parentId');

        $images = Image::where('parentId', $parentId)->get();

        return response()->json($images);
    }

    public function editFolder(Request $request)
    {
        $validatedData = $request->validate([
            'new_name' => 'required|string|max:255',
        ]);

        $id = $request->folder_id;

        $folder = Folder::findOrFail($id);
        $folder->folderName = $validatedData['new_name'];
        $folder->save();

        return response()->json(['message' => 'Folder edited successfully']);
    }

    public function deleteImage(Request $request)
    {
        try {
            $id = $request->imageId;
            $image = Image::findOrFail($id);

            Storage::delete('images/' . $image->filename);

            $image->delete();

            return response()->json(['message' => 'Image deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete image'], 500);
        }
    }

}
