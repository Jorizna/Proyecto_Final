@php $depth = $depth ?? 0; @endphp
<div class="reply-block">

    <div class="reply-item {{ $depth > 0 ? 'reply-item--nested' : '' }}"
         id="comment-{{ $comment->id }}">

        <a href="{{ route('usuarios.show', $comment->user) }}" class="reply-item__avatar-link">
            @if($comment->user->avatar)
                <img src="{{ asset('storage/' . $comment->user->avatar) }}"
                     alt="{{ $comment->user->name }}" class="avatar avatar--sm">
            @else
                <div class="avatar avatar--sm avatar--placeholder">
                    {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                </div>
            @endif
        </a>

        <div class="reply-item__body">

            <div class="reply-item__header">
                <a href="{{ route('usuarios.show', $comment->user) }}"
                   class="reply-item__author">{{ $comment->user->name }}</a>
                <span class="reply-item__fecha">{{ $comment->created_at->diffForHumans() }}</span>
                @if(auth()->id() === $comment->user_id)
                    <form method="POST"
                          action="{{ route('comentarios.destroy', [$publicacion, $comment]) }}"
                          class="reply-delete-form">
                        @csrf @method('DELETE')
                        <button type="button" class="btn-icon btn-icon--danger" title="Eliminar"
                                onclick="this.closest('form').submit()">✕</button>
                    </form>
                @endif
            </div>

            @if($comment->texto)
                <p class="reply-item__texto">{{ $comment->texto }}</p>
            @endif

            @if($comment->imagenes->isNotEmpty())
                @php $rn = min($comment->imagenes->count(), 4); @endphp
                <div class="fotos-grid fotos-grid--{{ $rn }} fotos-grid--sm">
                    @foreach($comment->imagenes->take(4) as $rim)
                        <div class="fotos-grid__item">
                            <img src="{{ asset('storage/' . $rim->ruta) }}" alt="">
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="reply-actions">
                @auth
                    <button class="reply-reply-btn"
                            onclick="toggleChildComposer({{ $comment->id }})">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" width="13" height="13">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                        Responder
                    </button>
                @endauth

                @if($comment->children->isNotEmpty())
                    <button class="ver-replies-btn"
                            onclick="toggleReplies({{ $comment->id }}, this)"
                            data-count="{{ $comment->children->count() }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" width="13" height="13">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                        Ver respuestas ({{ $comment->children->count() }})
                    </button>
                @endif
            </div>

            @auth
                <div id="child-composer-{{ $comment->id }}" class="reply-mini-composer">
                    <form method="POST" action="{{ route('comentarios.store', $publicacion) }}"
                          enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                        <textarea name="texto"
                                  placeholder="Responder a {{ $comment->user->name }}..."
                                  maxlength="1000" rows="2"></textarea>
                        <div class="reply-mini-composer__footer">
                            <label class="upload-btn upload-btn--xs" title="Adjuntar imagen">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="1.75" width="14" height="14">
                                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                    <polyline points="21 15 16 10 5 21"/>
                                </svg>
                                <input type="file" name="imagenes[]" accept="image/*">
                            </label>
                            <button type="submit" class="btn btn--primary btn--sm">Enviar</button>
                        </div>
                    </form>
                </div>
            @endauth

        </div>
    </div>
    @if($comment->children->isNotEmpty())
        <div class="reply-thread" id="replies-{{ $comment->id }}">
            @foreach($comment->children as $child)
                @include('publicaciones._reply', [
                    'comment'     => $child,
                    'depth'       => $depth + 1,
                    'publicacion' => $publicacion,
                ])
            @endforeach
        </div>
    @endif

</div>
