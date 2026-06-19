<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Basismodel voor gedeelde hulplogica in domeinmodellen.
abstract class BaseModel extends Model
{
    // Vult modelattributen op een veilige, herbruikbare manier.
    public function safeFill(array $attributes): static
    {
        $this->fill($attributes);

        return $this;
    }
}
