<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Module;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function csv(Request $request, Classroom $classroom): StreamedResponse
    {
        abort_unless($classroom->teacher_id === $request->user()->id || $request->user()->isAdmin(), 403);

        $modules = $classroom->modules()->get();
        $classroom->load(['students.progress']);

        $filename = 'nilai-' . $classroom->code . '-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($classroom, $modules) {
            $out = fopen('php://output', 'w');
            fputcsv($out, array_merge(['Nama', 'Email', 'Total XP'], $modules->map(fn ($m) => $m->title . ' (skor)')->all(), ['Status']));

            foreach ($classroom->students as $s) {
                $byModule = $s->progress->keyBy('module_id');
                $row = [$s->name, $s->email, $s->progress->sum('xp_earned')];
                $allPass = true;
                foreach ($modules as $m) {
                    $p = $byModule->get($m->id);
                    $row[] = $p ? $p->quiz_score : 0;
                    if (! $p || ! $p->is_completed) {
                        $allPass = false;
                    }
                }
                $row[] = $allPass ? 'Lulus' : 'Belum';
                fputcsv($out, $row);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
