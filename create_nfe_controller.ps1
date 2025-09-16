@'
<?php

namespace App\Http\Controllers;

use App\Models\Nfe;
use App\Models\NfeItem;
use App\Services\FocusNfeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NfeController extends Controller
{
    protected $focusNfeService;

    public function __construct(FocusNfeService $focusNfeService)
    {
        $this->middleware('auth');
        $this->focusNfeService = $focusNfeService;
    }

    public function index(Request $request)
    {
        $query = Nfe::with(['company'])
            ->where('company_id', Auth::user()->company_id)
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('numero')) {
            $query->where('numero', 'like', '%' . $request->numero . '%');
        }

        if ($request->filled('cliente')) {
            $query->where('destinatario_nome', 'like', '%' . $request->cliente . '%');
        }

        $nfes = $query->paginate(20);

        return view('nfe.index', compact('nfes'));
    }

    public function create()
    {
        return view('nfe.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('nfe.index')->with('success', 'NFe criada com sucesso!');
    }

    public function show(Nfe $nfe)
    {
        $nfe->load(['itens', 'company']);
        return view('nfe.show', compact('nfe'));
    }
}
'@ | Out-File -FilePath "app\Http\Controllers\NfeController.php" -Encoding UTF8
