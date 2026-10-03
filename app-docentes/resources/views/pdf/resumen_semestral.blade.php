<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resumen Semestral - {{ $profesor->name }} - {{ $periodo->codigo }}</title>
    <style>
        @page {
            margin: 1.2cm 1.5cm 1.5cm 1.5cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #1f2937;
            line-height: 1.35;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #065f46;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header h1 {
            font-size: 13px;
            font-weight: bold;
            margin: 0;
            color: #065f46;
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
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            padding: 6px 10px;
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            color: #065f46;
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
            padding: 4px 6px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f3f4f6;
            font-weight: bold;
            font-size: 9px;
            color: #374151;
            text-transform: uppercase;
        }
        .section-title {
            font-size: 10.5px;
            font-weight: bold;
            color: #065f46;
            border-bottom: 1px solid #065f46;
            padding-bottom: 2px;
            margin-top: 10px;
            margin-bottom: 6px;
            text-transform: uppercase;
        }
        .info-grid {
            margin-bottom: 10px;
        }
        .info-grid td {
            border: none;
            padding: 2px 4px;
        }
        .kpi-table th {
            text-align: center;
            background-color: #065f46;
            color: #ffffff;
            font-size: 9px;
        }
        .kpi-table td {
            text-align: center;
            font-weight: bold;
            font-size: 11px;
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
            font-size: 9px;
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
        Informe Semestral de Actividades Docentes — Semestre {{ $periodo->codigo }}
    </div>

    <!-- Identificación Docente -->
    <table class="info-grid">
        <tr>
            <td style="width: 18%; font-weight: bold;">Profesor:</td>
            <td style="width: 32%;">{{ $profesor->name }}</td>
            <td style="width: 18%; font-weight: bold;">Periodo:</td>
            <td style="width: 32%;">{{ $periodo->codigo }} ({{ $periodo->fecha_inicio?->format('d/m/Y') }} al {{ $periodo->fecha_fin?->format('d/m/Y') }})</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Documento:</td>
            <td>{{ $profesor->documento_identidad ?? 'No registrado' }}</td>
            <td style="font-weight: bold;">Correo:</td>
            <td>{{ $profesor->email }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Dedicación:</td>
            <td>{{ $profesor->dedicacion ?? 'No especificada' }}</td>
            <td style="font-weight: bold;">Categoría:</td>
            <td>{{ $profesor->categoria ?? 'No especificada' }}</td>
        </tr>
    </table>

    <!-- Indicadores Generales -->
    <table class="kpi-table">
        <thead>
            <tr>
                <th>Materias</th>
                <th>Grupos</th>
                <th>Actividades</th>
                <th>Registradas</th>
                <th>Horas Totales</th>
                <th>Estudiantes Impactados</th>
                <th>Evidencias</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $totales['materias'] }}</td>
                <td>{{ $totales['grupos'] }}</td>
                <td>{{ $totales['actividades'] }}</td>
                <td>{{ $totales['registradas'] }}</td>
                <td>{{ number_format($totales['horas'], 1) }} h</td>
                <td>{{ $totales['estudiantes'] }}</td>
                <td>{{ $totales['evidencias'] }}</td>
            </tr>
        </tbody>
    </table>

    <!-- 1. Asignaturas y Grupos -->
    <div class="section-title">1. Asignaturas y Grupos Impartidos</div>
    <table>
        <thead>
            <tr>
                <th style="width: 10%;">Código</th>
                <th style="width: 35%;">Asignatura</th>
                <th style="width: 8%;" class="text-center">Grupo</th>
                <th style="width: 25%;">Programa Curricular</th>
                <th style="width: 11%;" class="text-center">Estudiantes</th>
                <th style="width: 11%;" class="text-center">Horas Act.</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($por_materia as $item)
                <tr>
                    <td>{{ $item['codigo'] }}</td>
                    <td><strong>{{ $item['asignatura'] }}</strong></td>
                    <td class="text-center">G{{ $item['numero_grupo'] }}</td>
                    <td>{{ $item['programa'] }}</td>
                    <td class="text-center">{{ $item['estudiantes_inscritos'] }}</td>
                    <td class="text-center font-bold">{{ number_format($item['horas_acumuladas'], 1) }} h</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No registra grupos asignados en este periodo.</td>
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

    <!-- 3. Registro Cronológico de Actividades -->
    <div class="section-title">3. Detalle de Actividades Realizadas</div>
    <table>
        <thead>
            <tr>
                <th style="width: 9%;" class="text-center">Fecha</th>
                <th style="width: 28%;">Actividad / Título</th>
                <th style="width: 18%;">Materia(s)</th>
                <th style="width: 15%;">Tipo / Modalidad</th>
                <th style="width: 7%;" class="text-center">Horas</th>
                <th style="width: 8%;" class="text-center">Partic.</th>
                <th style="width: 8%;" class="text-center">Evid.</th>
                <th style="width: 7%;" class="text-center">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($actividades as $act)
                <tr>
                    <td class="text-center">{{ $act->fecha_inicio?->format('d/m/Y') }}</td>
                    <td>
                        <strong>{{ $act->titulo }}</strong>
                        @if ($act->nombre_invitado)
                            <div style="font-size: 8px; color: #4b5563;">Invitado: {{ $act->nombre_invitado }}</div>
                        @endif
                        @if ($act->entidadExterna)
                            <div style="font-size: 8px; color: #065f46;">Entidad: {{ $act->entidadExterna->nombre }}</div>
                        @endif
                    </td>
                    <td>
                        {{ $act->grupo?->asignatura?->codigo }} (G{{ $act->grupo?->numero_grupo }})
                        @if ($act->grupos->count() > 1)
                            <span style="font-size: 7.5px; color: #4f46e5;">[+{{ $act->grupos->count() - 1 }} grupos]</span>
                        @endif
                    </td>
                    <td>
                        {{ $act->tipoActividad?->nombre }}
                        <div style="font-size: 8px; color: #6b7280;">({{ ucfirst($act->modalidad) }})</div>
                    </td>
                    <td class="text-center font-bold">{{ $act->duracion_horas }}</td>
                    <td class="text-center">{{ $act->numero_estudiantes_participantes }}</td>
                    <td class="text-center">{{ $act->evidencias->count() }}</td>
                    <td class="text-center" style="font-size: 8px;">{{ ucfirst($act->estado) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No se han registrado actividades en este periodo.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- 4. Alineación con Criterios de Acreditación -->
    @if ($criterios->isNotEmpty())
        <div class="section-title">4. Tributación a Criterios de Acreditación</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">Código</th>
                    <th style="width: 70%;">Criterio / Factor de Acreditación</th>
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

    <!-- Firmas -->
    <div class="signatures">
        <table style="border: none; margin-top: 25px;">
            <tr>
                <td style="width: 50%; border: none; text-align: center;">
                    <div class="signature-line">
                        <strong>{{ $profesor->name }}</strong><br>
                        Profesor Responsable
                    </div>
                </td>
                <td style="width: 50%; border: none; text-align: center;">
                    <div class="signature-line">
                        <strong>Director(a) de Departamento</strong><br>
                        {{ config('institucion.departamento') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Footer fijo -->
    <div class="footer">
        {{ config('institucion.pie_pagina') }} &bull; Fecha de emisión: {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
