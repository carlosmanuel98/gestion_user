<?php

namespace App\Http\Controllers;

use App\Projects;
use App\Reports;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ChartController extends Controller
{
    private $parent = 'chart';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        /**
         *    id: 1, name: "Feature 1", series: [
         * { name: "Planned", start: new Date(2010,00,01), end: new Date(2010,00,03) },
         * { name: "Actual", start: new Date(2010,00,02), end: new Date(2010,00,05), color: "#f0f0f0" }
         * ]
         */

        $dataRaw = [];

        $data = Projects::orderBy('id', 'DESC')
            ->has('reports')#solo ti hay reportes
            ->with(['series',
            'series.issues' => function ($query) {
                $query->selectRaw('*, SUM(hours) as total_hours')
                    ->groupBy('report_id');
                }
            ])
            ->get();
        if ($data->isEmpty()) {
            // Redirige de vuelta al controlador principal con un mensaje de error
            return back()->withErrors(['No hay datos disponibles']);
        }    

        foreach ($data as $datum) {
            $clear_series = [];
        // dd($datum);
            
            foreach ($datum->series as $v) {
                // dd($v->issues->pluck('id')->first());
                // dd($v->description);

        //   dd($v->issues->pluck('total_hours')->first());
                $calc = $v->get_calc($v->id);
                if ($calc['start'] && $calc['end']) {
                    $clear_series[] = array_merge(
                        [
                            'color' => $v->color,
                            'title' => "<span><canvas class='pointer-color' style='background-color:" . $v->color . "'></canvas>  " . $v->name . "</span>",
                            'content' => $v->issues->pluck('total_hours')->first() . 'hs'
                        ],
                        $calc
                    );

                    $clear_series[] = [
                        'title' => '<span class=""> - Estimación</span>',
                        'start' => $v->start_at,
                        'end' => $v->deadline_at,
                        'content' => $v->issues->pluck('total_hours')->first() . ',00 hs'
                    ];


                    $dataRaw[] = array(
                        'id' => $v->issues->pluck('id')->first(), #ID DE LA TAREA/REPORTE
                        // 'id' => $datum['id'],
                        'name' => $datum['title'],
                        'series' => $clear_series
                    );
                }
            }
            // dd($dataRaw);
            // $dataRaw[] = array(
            //     'id' => $reportId,#ID DE LA TAREA/REPORTE
            //     // 'id' => $datum['id'],
            //     'name' => $datum['title'],
            //     'series' => $clear_series
            // );


            // dd($reportId);
        }

        return view($this->parent . '.view')->with(['data' => $dataRaw]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
