<?php

namespace Database\Seeders;

use App\Models\Etiqueta;
use App\Models\Publicacion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar todos los datos en orden correcto
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('notificaciones')->truncate();
        DB::table('jobs')->truncate();
        DB::table('imagenes_comentario')->truncate();
        DB::table('comentarios')->truncate();
        DB::table('favoritos')->truncate();
        DB::table('repostes')->truncate();
        DB::table('likes')->truncate();
        DB::table('follows')->truncate();
        DB::table('publicacion_etiqueta')->truncate();
        DB::table('imagenes')->truncate();
        DB::table('publicaciones')->truncate();
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->call(EtiquetaSeeder::class);

        $etiquetas = Etiqueta::all();

        // --- Moderador ---
        $mod = User::create([
            'name'     => 'Moderador FishSpot',
            'email'    => 'mod@fishspot.local',
            'password' => Hash::make('password'),
            'bio'      => 'Moderador oficial de la comunidad FishSpot España.',
            'rol'      => 'moderador',
        ]);

        // --- 19 usuarios normales ---
        $usuarios = [
            ['name' => 'Carlos Río',        'email' => 'carlos@fishspot.local',   'bio' => 'Pescador de trucha del Pirineo aragonés.'],
            ['name' => 'María Peñas',       'email' => 'maria@fishspot.local',    'bio' => 'Aficionada al spinning en embalses.'],
            ['name' => 'Javier Lomas',      'email' => 'javier@fishspot.local',   'bio' => 'Carpista de competición. Ebro es mi casa.'],
            ['name' => 'Ana Corrientes',    'email' => 'ana@fishspot.local',       'bio' => 'Pesca en aguas bravas y montaña.'],
            ['name' => 'Pedro Anzuelo',     'email' => 'pedro@fishspot.local',     'bio' => 'Todo terreno: río, mar y embalse.'],
            ['name' => 'Laura Trucha',      'email' => 'laura@fishspot.local',     'bio' => 'Mosca seca en ríos de montaña.'],
            ['name' => 'Miguel Ebro',       'email' => 'miguel@fishspot.local',    'bio' => 'Silurista del Ebro. 20 años en el río.'],
            ['name' => 'Sofía Costera',     'email' => 'sofia@fishspot.local',     'bio' => 'Pesca de costa en el Mediterráneo.'],
            ['name' => 'David Embalse',     'email' => 'david@fishspot.local',     'bio' => 'Black bass en embalses del centro.'],
            ['name' => 'Elena Pesca',       'email' => 'elena@fishspot.local',     'bio' => 'Iniciándome en el fly fishing.'],
            ['name' => 'Roberto Mar',       'email' => 'roberto@fishspot.local',   'bio' => 'Surfcasting y pesca de fondo en Cantabria.'],
            ['name' => 'Carmen Salmón',     'email' => 'carmen@fishspot.local',    'bio' => 'Salmón y trucha en el norte.'],
            ['name' => 'Antonio Lucio',     'email' => 'antonio@fishspot.local',   'bio' => 'Lucieros del Duero. La espera vale la pena.'],
            ['name' => 'Isabel Vadeador',   'email' => 'isabel@fishspot.local',    'bio' => 'Pesca con mosca y vadeando ríos.'],
            ['name' => 'Francisco Carpa',   'email' => 'francisco@fishspot.local', 'bio' => 'Carpista nocturno. El Manzanares y el Tajo.'],
            ['name' => 'Teresa Spinning',   'email' => 'teresa@fishspot.local',    'bio' => 'Spinning ligero en ríos pequeños.'],
            ['name' => 'Rodrigo Piraña',    'email' => 'rodrigo@fishspot.local',   'bio' => 'Aficionado al Black bass y la perca.'],
            ['name' => 'Natalia Coto',      'email' => 'natalia@fishspot.local',   'bio' => 'Cotos privados y pesca sin muerte.'],
            ['name' => 'Sergio Pantano',    'email' => 'sergio@fishspot.local',    'bio' => 'Pesca en pantanos de Andalucía.'],
        ];

        $users = [];
        foreach ($usuarios as $datos) {
            $users[] = User::create(array_merge($datos, [
                'password' => Hash::make('password'),
                'rol'      => 'user',
            ]));
        }

        // --- 30 publicaciones repartidas entre usuarios normales ---
        $publicacionesData = [
            ['titulo' => 'Río Gállego — tramo de montaña', 'descripcion' => 'Aguas cristalinas y truchas salvajes. El mejor tramo está entre Sabiñánigo y Biescas. Permiso de coto obligatorio en temporada alta.', 'lat' => 42.5652, 'lng' => -0.3614, 'temporada' => 'primavera', 'licencia' => 'coto', 'tags' => ['Trucha común', 'Trucha arco iris']],
            ['titulo' => 'Embalse de Mequinenza', 'descripcion' => 'El mar del Ebro. Siluro, carpa y lucio en abundancia. Puesto norte junto al canal, bueno para cebado.', 'lat' => 41.3740, 'lng' => 0.2940, 'temporada' => 'verano', 'licencia' => 'auton_1', 'tags' => ['Siluro', 'Carpa', 'Lucio']],
            ['titulo' => 'Río Noguera Pallaresa', 'descripcion' => 'Trucha arco iris de gran tamaño. Zona libre debajo del puente de Sort. Corriente fuerte, usar plomo pesado.', 'lat' => 42.4133, 'lng' => 0.9947, 'temporada' => 'verano', 'licencia' => 'auton_1', 'tags' => ['Trucha arco iris']],
            ['titulo' => 'Pantano de Benagéber', 'descripcion' => 'Black bass hasta 3 kg en la zona de cañaveral. Mejores horas al amanecer con señuelo superficial.', 'lat' => 39.7690, 'lng' => -1.1030, 'temporada' => 'verano', 'licencia' => 'auton_1', 'tags' => ['Black bass']],
            ['titulo' => 'Costa de Peñíscola', 'descripcion' => 'Dorada y lubina desde las rocas del norte del castillo. Mejor en marea entrante con gamba fresca.', 'lat' => 40.3626, 'lng' => 0.4073, 'temporada' => 'otono', 'licencia' => 'mar', 'tags' => ['Dorada', 'Lubina']],
            ['titulo' => 'Río Tajo — Aranjuez', 'descripcion' => 'Carpa y barbo en el tramo urbano. Poco presionado en días laborables. Buenas capturas con boilies de fresa.', 'lat' => 40.0333, 'lng' => -3.6000, 'temporada' => 'primavera', 'licencia' => 'auton_1', 'tags' => ['Carpa', 'Barbo']],
            ['titulo' => 'Embalse de Ullibarri-Gamboa', 'descripcion' => 'Lucio y perca en la zona de árboles sumergidos. Imprescindible el kayak para llegar a los mejores puestos.', 'lat' => 42.8904, 'lng' => -2.5290, 'temporada' => 'invierno', 'licencia' => 'auton_1', 'tags' => ['Lucio', 'Perca']],
            ['titulo' => 'Río Duero — Zamora', 'descripcion' => 'Zona de lucio del río Duero. Cebado con muertos naturales. Capturas frecuentes de 5 a 8 kg.', 'lat' => 41.5033, 'lng' => -5.7447, 'temporada' => 'invierno', 'licencia' => 'auton_1', 'tags' => ['Lucio', 'Carpa']],
            ['titulo' => 'Playa de la Malvarrosa — Valencia', 'descripcion' => 'Surfcasting nocturno. Corvallo y sargo hasta las 3 de la mañana. Cebo: calamar y gusano de arena.', 'lat' => 39.4828, 'lng' => -0.3220, 'temporada' => 'verano', 'licencia' => 'mar', 'tags' => ['Corvallo', 'Sargo']],
            ['titulo' => 'Río Ebro — Flix', 'descripcion' => 'Siluro gigante zona de la central. Capturas de más de 2 metros documentadas. Mejor de noche con muerto natural.', 'lat' => 41.2349, 'lng' => 0.5364, 'temporada' => 'verano', 'licencia' => 'auton_1', 'tags' => ['Siluro']],
            ['titulo' => 'Embalse de Cijara', 'descripcion' => 'Black bass y lucio. La zona de la presa vieja es espectacular al atardecer. Sin apenas presión pesquera.', 'lat' => 39.3060, 'lng' => -4.8550, 'temporada' => 'primavera', 'licencia' => 'auton_1', 'tags' => ['Black bass', 'Lucio']],
            ['titulo' => 'Río Sella — Asturias', 'descripcion' => 'Salmón atlántico en temporada. Zona libre debajo del puente medieval. Se necesita licencia especial de salmón.', 'lat' => 43.3620, 'lng' => -5.0790, 'temporada' => 'primavera', 'licencia' => 'coto', 'tags' => ['Trucha común']],
            ['titulo' => 'Costa de Tarifa', 'descripcion' => 'Atún rojo de superficie en verano. Jigging desde embarcación o desde el espigón. Capturas épicas.', 'lat' => 36.0143, 'lng' => -5.6043, 'temporada' => 'verano', 'licencia' => 'mar', 'tags' => ['Bacoreta', 'Lirio']],
            ['titulo' => 'Río Júcar — Alcira', 'descripcion' => 'Barbo y madrilla en el tramo bajo. Buena pesca de fondo con lombriz. Parking gratuito junto al puente.', 'lat' => 39.1530, 'lng' => -0.4330, 'temporada' => 'otono', 'licencia' => 'auton_1', 'tags' => ['Barbo']],
            ['titulo' => 'Embalse de La Breña II', 'descripcion' => 'Black bass de talla. Zona norte junto a los arbustos sumergidos. Técnica de pesca: drop shot con anguila artificial.', 'lat' => 37.9610, 'lng' => -5.0180, 'temporada' => 'verano', 'licencia' => 'auton_1', 'tags' => ['Black bass', 'Perca']],
            ['titulo' => 'Río Ara — Broto', 'descripcion' => 'Trucha salvaje en aguas pirenaicas. Pesca con mosca seca en el tramo alto. Capturas y suelta obligatorio.', 'lat' => 42.6010, 'lng' => -0.0590, 'temporada' => 'primavera', 'licencia' => 'coto', 'tags' => ['Trucha común', 'Trucha arco iris']],
            ['titulo' => 'Cabo de Gata — Almería', 'descripcion' => 'Dentón y sargo desde las rocas volcánicas. Aguas cristalinas del Mediterráneo. Mejor con lancha pequeña.', 'lat' => 36.7292, 'lng' => -2.1871, 'temporada' => 'verano', 'licencia' => 'mar', 'tags' => ['Dentón', 'Sargo']],
            ['titulo' => 'Río Segura — Cieza', 'descripcion' => 'Carpa en el tramo lento tras el azud. Boilies de vainilla con fondo de PVA. Zona sombreada por álamos.', 'lat' => 38.2390, 'lng' => -1.4190, 'temporada' => 'verano', 'licencia' => 'auton_1', 'tags' => ['Carpa', 'Barbo']],
            ['titulo' => 'Puerto de Motril', 'descripcion' => 'Jurel y bacoreta desde el dique exterior. Curricán desde embarcación muy productivo al amanecer.', 'lat' => 36.7233, 'lng' => -3.5120, 'temporada' => 'otono', 'licencia' => 'mar', 'tags' => ['Jurel', 'Bacoreta']],
            ['titulo' => 'Embalse de Riaño', 'descripcion' => 'Truchas grandes en aguas frías de montaña. Zona de la vieja carretera sumergida, buena para el jigging vertical.', 'lat' => 42.9930, 'lng' => -5.0090, 'temporada' => 'invierno', 'licencia' => 'auton_5', 'tags' => ['Trucha arco iris', 'Perca']],
            ['titulo' => 'Río Tera — Zamora', 'descripcion' => 'Tenca en los remansos del río Tera. Poco conocida y sin presión pesquera. Cebo: guisante cocido o maíz.', 'lat' => 42.0540, 'lng' => -6.2110, 'temporada' => 'verano', 'licencia' => 'auton_1', 'tags' => ['Tenca', 'Carpa']],
            ['titulo' => 'Cabo Peñas — Asturias', 'descripcion' => 'Lubina de roca en temporada de mareas vivas. Señuelo: minnow flotante de 9 cm. Zona expuesta, cuidado con el oleaje.', 'lat' => 43.6580, 'lng' => -5.8420, 'temporada' => 'invierno', 'licencia' => 'mar', 'tags' => ['Lubina']],
            ['titulo' => 'Río Cinca — Monzón', 'descripcion' => 'Barbo y boga en el tramo medio del Cinca. Pesca fácil para principiantes. Buena zona para iniciarse con el morcón.', 'lat' => 41.9110, 'lng' => 0.1890, 'temporada' => 'primavera', 'licencia' => 'auton_1', 'tags' => ['Barbo', 'Boga']],
            ['titulo' => 'Embalse de Orellana', 'descripcion' => 'El paraíso del Black bass en Extremadura. Embarcación recomendada. Las cañas de junco sumergidas son el mejor spot.', 'lat' => 39.0250, 'lng' => -5.5370, 'temporada' => 'primavera', 'licencia' => 'auton_1', 'tags' => ['Black bass', 'Lucio']],
            ['titulo' => 'Playa de Ondarreta — San Sebastián', 'descripcion' => 'Lubina de surf desde la playa al amanecer. Cebo vivo (lanzón) muy efectivo. Capturas frecuentes de 2-4 kg.', 'lat' => 43.3180, 'lng' => -2.0010, 'temporada' => 'otono', 'licencia' => 'mar', 'tags' => ['Lubina']],
            ['titulo' => 'Río Jalón — Calatayud', 'descripcion' => 'Barbo mediterráneo y madrilla en el Jalón. Zona urbana tranquila. Buen acceso y aparcamiento fácil.', 'lat' => 41.3547, 'lng' => -1.6423, 'temporada' => 'primavera', 'licencia' => 'auton_1', 'tags' => ['Barbo', 'Madrilla']],
            ['titulo' => 'Embalse de Sau', 'descripcion' => 'Perca y black bass junto a la iglesia sumergida. Spot muy fotogénico. Mejor acceso en kayak desde el camping.', 'lat' => 41.9760, 'lng' => 2.3890, 'temporada' => 'primavera', 'licencia' => 'auton_1', 'tags' => ['Perca', 'Black bass']],
            ['titulo' => 'Ría de Arousa — Galicia', 'descripcion' => 'Lubina y sargo en la ría. Pesca desde embarcación o desde los muelles de las bateas. Marea baja es la mejor.', 'lat' => 42.5510, 'lng' => -8.8820, 'temporada' => 'otono', 'licencia' => 'mar', 'tags' => ['Lubina', 'Sargo']],
            ['titulo' => 'Río Guadalquivir — Córdoba', 'descripcion' => 'Carpa y barbo en el tramo urbano de Córdoba. Zona junto a la mezquita. Pesca tranquila con vistas históricas.', 'lat' => 37.8791, 'lng' => -4.7793, 'temporada' => 'primavera', 'licencia' => 'auton_1', 'tags' => ['Carpa', 'Barbo']],
            ['titulo' => 'Embalse de Buendía', 'descripcion' => 'Lucio y black bass en las colas del embalse. Zona de vegetación acuática, ideal para pesca de superficie con poppers.', 'lat' => 40.3720, 'lng' => -2.7670, 'temporada' => 'verano', 'licencia' => 'auton_1', 'tags' => ['Lucio', 'Black bass']],
        ];

        foreach ($publicacionesData as $i => $datos) {
            $user = $users[$i % count($users)];

            $pub = Publicacion::create([
                'user_id'     => $user->id,
                'titulo'      => $datos['titulo'],
                'descripcion' => $datos['descripcion'],
                'latitud'     => $datos['lat'],
                'longitud'    => $datos['lng'],
                'temporada'   => $datos['temporada'],
                'licencia'    => $datos['licencia'],
            ]);

            $tagIds = $etiquetas->whereIn('nombre', $datos['tags'])->pluck('id');
            $pub->etiquetas()->sync($tagIds);
        }

        // --- Follows: cada usuario sigue a 3-5 aleatorios ---
        foreach ($users as $follower) {
            $targets = collect($users)->filter(fn($u) => $u->id !== $follower->id)->random(rand(3, 5));
            foreach ($targets as $target) {
                $follower->following()->syncWithoutDetaching([$target->id]);
            }
        }

        // --- Likes: distribuidos aleatoriamente ---
        $publicaciones = Publicacion::all();
        foreach ($users as $user) {
            $toLike = $publicaciones->where('user_id', '!=', $user->id)->random(rand(3, 8));
            foreach ($toLike as $pub) {
                DB::table('likes')->insertOrIgnore([
                    'user_id'        => $user->id,
                    'publicacion_id' => $pub->id,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
        }
    }
}
