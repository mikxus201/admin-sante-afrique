{{-- Bande 1 : Back-office | connecté : ... | se déconnecter --}}
<div
  style="
    position:sticky; top:0; z-index:1000;
    background:#f8fafc; border-bottom:1px solid #e5e7eb;
    margin-left:calc(50% - 50vw); margin-right:calc(50% - 50vw); width:100vw;
  "
>
  <div
    style="
      max-width:1200px; margin:0 auto; padding:8px 12px;
      display:grid; grid-template-columns:1fr auto 1fr; align-items:center; column-gap:12px;
      color:#6b7280; font-size:14px;
    "
  >
    {{-- Gauche --}}
    <div style="justify-self:start; white-space:nowrap;">
      <a href="{{ route('admin.dashboard') }}" style="text-decoration:none; color:inherit;">
        Back-office
      </a>
    </div>

    {{-- Centre --}}
    <div style="justify-self:center; text-align:center; white-space:nowrap;">
      @php($u = auth()->user())
      connecté : <strong>Super Admin</strong>
      (<strong>{{ $u->username ?? $u->name ?? $u->email }}</strong>)
    </div>

    {{-- Droite --}}
    <div style="justify-self:end; white-space:nowrap;">
      <form action="{{ route('logout') }}" method="POST" style="display:inline;">
        @csrf
        <button type="submit"
                style="background:none; border:none; padding:0; cursor:pointer; color:#6b7280;">
          se déconnecter
        </button>
      </form>
    </div>
  </div>
</div>
