<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consolidado Departamental - {{ $periodo->codigo }}</title>
    <style>
        @page {
            margin: 1.2cm 1.5cm 1.5cm 1.5cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9.5px;
            color: #1f2937;
            line-height: 1.3;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #581c87;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 13px;
            font-weight: bold;
            margin: 0;
            color: #581c87;
            text-transform: uppercase;
        }
        .header h2 {
            font-size: 11px;
            font-weight: 600;
            margin: 2px 0;
            color: #374151;
        }
        .header h3 {
            font-size: 10px;
            font-weight: normal;
            margin: 0;
            color: #4b5563;
        }
        .title-box {
            background-color: #faf5ff;
            border: 1px solid #e9d5ff;
            padding: 6px 10px;
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            color: #581c87;
            margin-bottom: 12px;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 4px 5px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f3f4f6;
            font-weight: bold;
            font-size: 8.5px;
            color: #374151;
            text-transform: uppercase;
        }
        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: #581c87;
            border-bottom: 1px solid #581c87;
            padding-bottom: 2px;
            margin-top: 10px;
            margin-bottom: 6px;
            text-transform: uppercase;
        }
        .kpi-table th {
            text-align: center;
            background-color: #581c87;
            color: #ffffff;
            font-size: 8.5px;
        }
        .kpi-table td {
            text-align: center;
            font-weight: bold;
            font-size: 10.5px;
            background-color: #f9fafb;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 4px;
        }
        .signatures {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .signature-line {
            border-top: 1px solid #374151;
            width: 70%;
            margin: 0 auto;
            padding-top: 4px;
            text-align: center;
            font-size: 8.5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('institucion.universidad') }}</h1>
        <h2>{{ config('institucion.facultad') }} — {{ config('institucion.sede') }}</h2>
        <h3>{{ config('institucion.departamento') }}</h3>
    </div>

    <div class="title-box">
        Consolidado Departamental de Actividades Docentes — Semestre {{ $periodo->codigo }}
    </div>

    <table style="border: none; margin-bottom: 8px;">
        <tr>
            <td style="border: none; width: 50%;"><strong>Periodo Académico:</strong> {{ $periodo->codigo }} ({{ $periodo->fecha_inicio?->format('d/m/Y') }} al {{ $periodo->fecha_fin?->format('d/m/Y') }})</td>
            <td style="border: none; width: 50%; text-align: right;"><strong>Fecha de Corte:</strong> {{ now()->format('d/m/Y H:i') }}</td>
        </tr>
    </table>

    <!-- Indicadores Globales -->
    <table class="kpi-table">
        <thead>
            <tr>
                <th>Docentes</th>
                <th>Grupos</th>
                <th>Asignaturas</th>
                <th>Actividades</th>
                <th>Registradas</th>
                <th>Horas Totales</th>
                <th>Impacto Estudiantes</th>
                <th>Evidencias</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $totales['docentes_activos'] }}</td>
                <td>{{ $totales['grupos_ofertados'] }}</td>
                <td>{{ $totales['asignaturas_distintas'] }}</td>
                <td>{{ $totales['actividades'] }}</td>
                <td>{{ $totales['registradas'] }}</td>
                <td>{{ number_format($totales['horas'], 1) }} h</td>
                <td>{{ $totales['estudiantes'] }}</td>
                <td>{{ $totales['evidencias'] }}</td>
            </tr>
        </tbody>
    </table>

    <!-- 1. Desglose por Docente -->
    <div class="section-title">1. Resumen por Profesor</div>
    <table>
        <thead>
            <tr>
                <th style="width: 25%;">Profesor</th>
                <th style="width: 12%;">Documento</th>
                <th style="width: 13%;">Dedicación</th>
                <th style="width: 8%;" class="text-center">Grupos</th>
                <th style="width: 10%;" class="text-center">Actividades</th>
                <th style="width: 10%;" class="text-center">Registradas</th>
                <th style="width: 10%;" class="text-center">Horas</th>
                <th style="width: 12%;" class="text-center">Evidencias</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($por_docente as $doc)
                <tr>
                    <td><strong>{{ $doc['nombre'] }}</strong></td>
                    <td>{{ $doc['documento'] }}</td>
                    <td>{{ $doc['dedicacion'] }}</td>
                    <td class="text-center">{{ $doc['grupos_count'] }}</td>
                    <td class="text-center font-bold">{{ $doc['actividades_count'] }}</td>
                    <td class="text-center">{{ $doc['registradas_count'] }}</td>
                    <td class="text-center font-bold">{{ number_format($doc['horas_totales'], 1) }} h</td>
                    <td class="text-center">{{ $doc['evidencias_count'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No hay docentes con materias asignadas en este periodo.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- 2. Clasificación por Tipo de Actividad -->
    @if ($por_tipo->isNotEmpty())
        <div class="section-title">2. Distribución por Tipo de Actividad</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 45%;">Tipo de Actividad</th>
                    <th style="width: 20%;">Categoría</th>
                    <th style="width: 15%;" class="text-center">Cantidad</th>
                    <th style="width: 20%;" class="text-center">Horas Totales</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($por_tipo as $t)
                    <tr>
                        <td><strong>{{ $t['nombre'] }}</strong></td>
                        <td>{{ ucfirst($t['categoria']) }}</td>
                        <td class="text-center">{{ $t['cantidad'] }}</td>
                        <td class="text-center font-bold">{{ number_format($t['horas'], 1) }} h</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- 3. Distribución por Programa Curricular -->
    @if ($por_programa->isNotEmpty())
        <div class="section-title">3. Distribución por Programa Curricular</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 45%;">Programa Curricular</th>
                    <th style="width: 15%;" class="text-center">Grupos</th>
                    <th style="width: 20%;" class="text-center">Estudiantes Matriculados</th>
                    <th style="width: 20%;" class="text-center">Actividades Registradas</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($por_programa as $prog)
                    <tr>
                        <td><strong>{{ $prog['programa'] }}</strong></td>
                        <td class="text-center">{{ $prog['grupos_count'] }}</td>
                        <td class="text-center">{{ $prog['estudiantes_matriculados'] }}</td>
                        <td class="text-center font-bold">{{ $prog['actividades_count'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- 4. Criterios de Acreditación -->
    @if ($criterios->isNotEmpty())
        <div class="section-title">4. Tributación a Criterios de Acreditación Institucional</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">Código</th>
                    <th style="width: 70%;">Criterio / Factor</th>
                    <th style="width: 15%;" class="text-center">Actividades</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($criterios as $crit)
                    <tr>
                        <td><strong>{{ $crit['codigo'] }}</strong></td>
                        <td>{{ $crit['nombre'] }}</td>
                        <td class="text-center font-bold">{{ $crit['cantidad_actividades'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Firmas Oficiales -->
    <div class="signatures">
        <table style="border: none; margin-top: 30px;">
            <tr>
                <td style="width: 50%; border: none; text-align: center;">
                    <div class="signature-line">
                        <strong>Director(a) de Departamento</strong><br>
                        {{ config('institucion.departamento') }}
                    </div>
                </td>
                <td style="width: 50%; border: none; text-align: center;">
                    <div class="signature-line">
                        <strong>Vicedecano(a) Académico(a)</strong><br>
                        {{ config('institucion.facultad') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Footer fijo -->
    <div class="footer">
        {{ config('institucion.pie_pagina') }} &bull; Emisión Oficial Administrativa
    </div>
</body>
</html>
