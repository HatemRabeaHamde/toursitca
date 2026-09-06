@switch($icon)
    @case('car')
    @case('bus')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 17h14l-1-7H6z"/><path d="M7 10l1-3h8l1 3"/><circle cx="8" cy="18" r="1.5"/><circle cx="16" cy="18" r="1.5"/></svg>
        @break
    @case('boat')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 15h16l-2 4H6z"/><path d="M8 15V7l8 3v5"/><path d="M3 21c2 1 4 1 6 0s4-1 6 0 4 1 6 0"/></svg>
        @break
    @case('food')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 2v8"/><path d="M10 2v8"/><path d="M4 2v8a3 3 0 0 0 6 0"/><path d="M16 2v20"/><path d="M16 2c3 2 4 5 2 8"/></svg>
        @break
    @case('photo')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h4l2-3h4l2 3h4v13H4z"/><circle cx="12" cy="13" r="4"/></svg>
        @break
    @case('camp')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 20 12 4l9 16"/><path d="M9 20l3-6 3 6"/><path d="M12 4v16"/></svg>
        @break
    @case('finish')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 21V4"/><path d="M5 4h12l-2 4 2 4H5"/></svg>
        @break
    @case('walk')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="13" cy="4" r="2"/><path d="m10 21 2-7-3-3 2-4 4 2 2 3"/><path d="m8 12-3 2"/><path d="m14 14 4 7"/></svg>
        @break
    @default
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s7-5 7-12a7 7 0 1 0-14 0c0 7 7 12 7 12z"/><circle cx="12" cy="9" r="2.5"/></svg>
@endswitch
