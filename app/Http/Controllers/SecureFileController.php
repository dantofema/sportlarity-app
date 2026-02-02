<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SecureFileController extends Controller
{
    /**
     * Descargar avatar de usuario
     */
    public function downloadAvatar(Request $request, string $filename): StreamedResponse
    {
        // Verificar que el usuario esté autenticado
        if (! auth()->check()) {
            abort(403, 'No autorizado');
        }

        $path = 'private/avatars/'.$filename;

        // Verificar que el archivo existe
        if (! Storage::disk('local')->exists($path)) {
            abort(404, 'Archivo no encontrado');
        }

        return Storage::disk('local')->download($path);
    }

    /**
     * Descargar documento
     */
    public function downloadDocument(Request $request, int $id): StreamedResponse
    {
        // Verificar que el usuario esté autenticado
        if (! auth()->check()) {
            abort(403, 'No autorizado');
        }

        $document = Document::findOrFail($id);

        // Verificar permisos: usuarios wellness solo pueden ver sus propios documentos
        if (auth()->user()->hasRole('wellness')) {
            if ($document->user_id !== auth()->id()) {
                abort(403, 'No tienes permiso para acceder a este documento');
            }
        }

        // Verificar que el archivo existe
        if (! Storage::disk('private_documents')->exists($document->file)) {
            abort(404, 'Archivo no encontrado');
        }

        return Storage::disk('private_documents')->download($document->file);
    }

    /**
     * Descargar imagen de documento
     */
    public function downloadDocumentImage(Request $request, int $id): StreamedResponse
    {
        // Verificar que el usuario esté autenticado
        if (! auth()->check()) {
            abort(403, 'No autorizado');
        }

        $document = Document::findOrFail($id);

        // Verificar permisos
        if (auth()->user()->hasRole('wellness')) {
            if ($document->user_id !== auth()->id()) {
                abort(403, 'No tienes permiso para acceder a esta imagen');
            }
        }

        // Verificar que el archivo existe
        if (! $document->image || ! Storage::disk('private_documents')->exists($document->image)) {
            abort(404, 'Imagen no encontrada');
        }

        return Storage::disk('private_documents')->download($document->image);
    }

    /**
     * Descargar archivo de feedback
     */
    public function downloadFeedback(Request $request, int $id): StreamedResponse
    {
        // Verificar que el usuario esté autenticado
        if (! auth()->check()) {
            abort(403, 'No autorizado');
        }

        $feedback = Feedback::findOrFail($id);

        // Verificar permisos: usuarios wellness solo pueden ver su propio feedback
        if (auth()->user()->hasRole('wellness')) {
            if ($feedback->user_id !== auth()->id()) {
                abort(403, 'No tienes permiso para acceder a este feedback');
            }
        }

        // Verificar que el archivo existe
        if (! $feedback->file || ! Storage::disk('private_feedback')->exists($feedback->file)) {
            abort(404, 'Archivo no encontrado');
        }

        return Storage::disk('private_feedback')->download($feedback->file);
    }
}
