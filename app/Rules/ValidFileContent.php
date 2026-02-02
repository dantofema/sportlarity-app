<?php

namespace App\Rules;

use Illuminate\Translation\PotentiallyTranslatedString;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class ValidFileContent implements ValidationRule
{
    protected array $allowedMimeTypes;

    /**
     * Magic bytes (firmas de archivos) para validación
     */
    protected array $mimeSignatures = [
        'application/pdf' => ['25504446'],
        'image/jpeg' => ['FFD8FF'],
        'image/png' => ['89504E47'],
        'image/webp' => ['52494646'], // "RIFF"
        'application/msword' => ['D0CF11E0'],
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['504B0304'],
        'application/vnd.ms-excel' => ['D0CF11E0'],
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => ['504B0304'],
        'application/vnd.ms-powerpoint' => ['D0CF11E0'],
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => ['504B0304'],
        'text/plain' => [], // Los archivos de texto no tienen firma específica
        'text/csv' => [],
    ];

    public function __construct(array $allowedMimeTypes)
    {
        $this->allowedMimeTypes = $allowedMimeTypes;
    }

    /**
     * Run the validation rule.
     *
     * @param Closure(string, ?string=):PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile) {
            $fail('El archivo no es válido.');

            return;
        }

        // Obtener el MIME type reportado por el archivo
        $mimeType = $value->getMimeType();

        // Verificar que el MIME type esté en la lista permitida
        if (! in_array($mimeType, $this->allowedMimeTypes)) {
            $fail('El tipo de archivo no está permitido.');

            return;
        }

        // Leer los primeros bytes del archivo para validar la firma
        $handle = fopen($value->getRealPath(), 'rb');
        if (! $handle) {
            $fail('No se pudo leer el archivo.');

            return;
        }

        $bytes = bin2hex(fread($handle, 8));
        fclose($handle);

        // Si el MIME type no tiene firmas definidas (como text/plain), aceptarlo
        if (! isset($this->mimeSignatures[$mimeType]) || empty($this->mimeSignatures[$mimeType])) {
            return;
        }

        // Verificar que los bytes coincidan con alguna firma válida
        $isValid = false;
        foreach ($this->mimeSignatures[$mimeType] as $signature) {
            if (str_starts_with(strtoupper($bytes), $signature)) {
                $isValid = true;
                break;
            }
        }

        if (! $isValid) {
            $fail('El contenido del archivo no corresponde con su tipo declarado.');
        }
    }
}
