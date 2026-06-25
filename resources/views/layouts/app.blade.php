<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'JACB Time Tracker')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <script src="https://code.iconify.design/iconify-icon/3.0.0/iconify-icon.min.js"></script>
    <style>
        /* ─── Design tokens ─── */
        :root {
            --ink: #101030;
            --teal: #00b0b0;
            --teal-dark: #008a8a;
            --teal-soft: #e0f7f7;
            --teal-mid: #00c0c0;
            --slate: #6b7a8a;
            --slate-light: #9aa3ad;
            --bg: #f2f5f8;
            --card: #ffffff;
            --border: #dde3ea;
            --amber: #b8741f;
            --amber-soft: #fdf2e3;
            --red: #b8412f;
            --red-soft: #fbeae6;
            --purple: #6b3fa0;
            --purple-soft: #ece4f7;
            --blue: #2563eb;
            --blue-soft: #eff6ff;
            --radius: 12px;
            --sidebar-w: 230px;
            --header-h: 60px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg);
            color: var(--ink);
            font-size: 14px;
            line-height: 1.5;
        }

        a {
            color: var(--teal);
            text-decoration: none;
        }

        a:hover {
            color: var(--teal-dark);
        }

        .mono {
            font-family: 'IBM Plex Mono', monospace;
        }

        /* ─── Sidebar ─── */
        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: var(--sidebar-w);
            background: #101030;
            display: flex;
            flex-direction: column;
            z-index: 200;
            transition: transform 0.25s ease;
            overflow: hidden;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            text-decoration: none;
            flex-shrink: 0;
        }

        .sidebar-brand .brand-logo {
            width: 140px;
            height: 140px;
            object-fit: contain;
            border-radius: 50%;
            display: block;
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            flex-shrink: 0;
        }

        .sidebar-user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #00b0b0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        .sidebar-user-info {
            flex: 1;
            min-width: 0;
        }

        .sidebar-user-name {
            font-size: 12.5px;
            font-weight: 600;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-user-role {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.4);
            text-transform: capitalize;
        }

        /* ─── Sidebar Nav ─── */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 10px 0;
            list-style: none;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 3px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
        }

        .sidebar-nav-header {
            padding: 10px 20px 4px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.28);
        }

        .sidebar-nav li a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 20px;
            color: rgba(255, 255, 255, 0.58);
            font-size: 13px;
            font-weight: 500;
            border-left: 3px solid transparent;
            transition: all 0.18s;
            text-decoration: none;
        }

        .sidebar-nav li a:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.05);
            border-left-color: rgba(0, 192, 192, 0.45);
        }

        .sidebar-nav li a.active {
            color: #fff;
            background: rgba(0, 192, 192, 0.15);
            border-left-color: #00c0c0;
        }

        .sidebar-nav li a iconify-icon,
        .sidebar-nav li a i.bi {
            font-size: 16px;
            flex-shrink: 0;
            color: inherit;
            opacity: 0.85;
            width: 18px;
            text-align: center;
        }

        /* Badge in sidebar nav */
        .sidebar-nav li a .nav-badge {
            margin-left: auto;
            background: #e05555;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 20px;
            line-height: 1.6;
        }

        .sidebar-logout {
            padding: 14px 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            flex-shrink: 0;
        }

        .sidebar-logout form button {
            display: flex;
            align-items: center;
            gap: 8px;
            background: none;
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.48);
            font-size: 12.5px;
            padding: 7px 14px;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-family: inherit;
            transition: all 0.18s;
        }

        .sidebar-logout form button:hover {
            background: rgba(255, 255, 255, 0.06);
            color: rgba(255, 255, 255, 0.85);
        }

        /* ─── Top Header ─── */
        .nb-header {
            position: fixed;
            top: 0;
            right: 0;
            left: var(--sidebar-w);
            height: var(--header-h);
            background: #fff;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            z-index: 100;
            transition: left 0.25s ease;
        }

        .nb-header__left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .nb-header__menu-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            color: var(--slate);
            display: flex;
            align-items: center;
            transition: background 0.18s;
        }

        .nb-header__menu-btn:hover {
            background: var(--bg);
            color: var(--ink);
        }

        .nb-header__search {
            position: relative;
            display: flex;
            align-items: center;
        }

        .nb-header__search-icon {
            position: absolute;
            left: 11px;
            color: #aaa;
            pointer-events: none;
        }

        .nb-header__search-input {
            width: 210px;
            padding: 8px 12px 8px 34px;
            border: none;
            background: var(--bg);
            border-radius: 50px;
            font-size: 13px;
            color: var(--ink);
            outline: none;
            font-family: inherit;
        }

        .nb-header__search-input:focus {
            background: #ebeef2;
        }

        .nb-header__right {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nb-header__server-time {
            font-size: 12px;
            color: var(--slate);
            padding: 8px 10px;
            white-space: nowrap;
            font-family: 'IBM Plex Mono', monospace;
        }

        /* Profile dropdown */
        .nb-header__profile {
            position: relative;
        }

        .nb-header__profile-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px 10px;
            border-radius: 10px;
            font-family: inherit;
            transition: background 0.18s;
        }

        .nb-header__profile-btn:hover {
            background: var(--bg);
        }

        .nb-header__avatar-placeholder {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #00b0b0;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
        }

        .nb-header__profile-info {
            text-align: left;
        }

        .nb-header__profile-name {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--ink);
        }

        .nb-header__profile-role {
            display: block;
            font-size: 11px;
            color: var(--slate);
            text-transform: capitalize;
        }

        .nb-header__profile-chevron {
            color: var(--slate);
        }

        /* ── Dropdown shared ── */
        .nb-dropdown {
            display: none;
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: 0 8px 28px rgba(15, 31, 45, 0.12);
            min-width: 180px;
            z-index: 300;
            overflow: hidden;
        }

        .nb-dropdown.open {
            display: block;
        }

        .nb-dropdown__item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 16px;
            font-size: 13.5px;
            color: var(--ink);
            transition: background 0.15s;
            text-decoration: none;
        }

        .nb-dropdown__item:hover {
            background: var(--bg);
        }

        .nb-dropdown__item--logout {
            color: var(--red);
            border-top: 1px solid var(--border);
        }

        .nb-header__logout-form {
            display: none;
        }

        /* ─── Main content ─── */
        #main-container {
            margin-left: var(--sidebar-w);
            padding-top: var(--header-h);
            min-height: 100vh;
            transition: margin-left 0.25s ease;
        }

        .page-content {
            padding: 28px 28px 60px;
        }

        /* ─── Page head ─── */
        .page-head {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            margin-bottom: 22px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .page-head h1 {
            font-size: 21px;
            font-weight: 700;
            margin: 0;
        }

        .page-head p.subtitle {
            color: var(--slate);
            font-size: 13px;
            margin: 4px 0 0;
        }

        /* ─── Stat cards ─── */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 18px 20px;
            box-shadow: 0 2px 8px rgba(15, 31, 45, 0.04);
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }

        .stat-card__icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 20px;
        }

        .stat-card__icon.teal {
            background: var(--teal-soft);
            color: var(--teal-dark);
        }

        .stat-card__icon.amber {
            background: var(--amber-soft);
            color: var(--amber);
        }

        .stat-card__icon.purple {
            background: var(--purple-soft);
            color: var(--purple);
        }

        .stat-card__icon.blue {
            background: var(--blue-soft);
            color: var(--blue);
        }

        .stat-card__icon.red {
            background: var(--red-soft);
            color: var(--red);
        }

        .stat-card__body {
            flex: 1;
            min-width: 0;
        }

        .stat-card__num {
            font-size: 26px;
            font-weight: 700;
            font-family: 'IBM Plex Mono', monospace;
            color: var(--ink);
            line-height: 1.1;
        }

        .stat-card__label {
            font-size: 11px;
            color: var(--slate);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 4px;
        }

        /* ─── Legacy stat-row ─── */
        .stat-row {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .stat {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 18px 22px;
            flex: 1;
            min-width: 140px;
            box-shadow: 0 2px 8px rgba(15, 31, 45, 0.04);
        }

        .stat .num {
            font-size: 26px;
            font-weight: 700;
            font-family: 'IBM Plex Mono', monospace;
            color: #008a8a;
        }

        .stat .label {
            font-size: 11px;
            color: var(--slate);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 4px;
        }

        /* ─── Cards ─── */
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 22px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(15, 31, 45, 0.04);
        }

        .card h2 {
            font-size: 14.5px;
            font-weight: 700;
            margin: 0 0 16px;
            color: var(--ink);
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            flex-wrap: wrap;
            gap: 8px;
        }

        .card-header h2 {
            margin: 0;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 760px) {
            .grid-2 {
                grid-template-columns: 1fr;
            }
        }

        /* ─── Tables ─── */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th {
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--slate);
            padding: 9px 12px;
            border-bottom: 1.5px solid var(--border);
            font-weight: 700;
            background: #f8fafc;
        }

        table td {
            padding: 12px 12px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        table tbody tr:hover {
            background: #f9fbfa;
        }

        table.compact td,
        table.compact th {
            padding: 7px 12px;
        }

        /* ─── Badges ─── */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-running {
            background: var(--amber-soft);
            color: var(--amber);
        }

        .badge-completed {
            background: var(--teal-soft);
            color: var(--teal-dark);
        }

        .badge-admin {
            background: #ece4f7;
            color: #6b3fa0;
        }

        .badge-member {
            background: #e8eef7;
            color: #3a5a8c;
        }

        .badge-active {
            background: var(--teal-soft);
            color: var(--teal-dark);
        }

        .badge-inactive {
            background: #f1f3f5;
            color: var(--slate);
        }

        .badge-pending {
            background: var(--amber-soft);
            color: var(--amber);
        }

        .badge-approved {
            background: var(--teal-soft);
            color: var(--teal-dark);
        }

        .badge-rejected {
            background: var(--red-soft);
            color: var(--red);
        }

        .badge-cancelled {
            background: #f1f3f5;
            color: var(--slate);
        }

        .badge-paid {
            background: var(--teal-soft);
            color: var(--teal-dark);
        }

        .badge-unpaid {
            background: var(--amber-soft);
            color: var(--amber);
        }

        /* ─── Buttons ─── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: none;
            border-radius: 8px;
            padding: 9px 16px;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.18s;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--teal);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--teal-dark);
            color: #fff;
        }

        .btn-success {
            background: #16a34a;
            color: #fff;
        }

        .btn-success:hover {
            background: #15803d;
            color: #fff;
        }

        .btn-danger {
            background: var(--red-soft);
            color: var(--red);
        }

        .btn-danger:hover {
            background: #f3d9d2;
        }

        .btn-ghost {
            background: transparent;
            color: var(--slate);
            border: 1px solid var(--border);
        }

        .btn-ghost:hover {
            background: var(--bg);
        }

        .btn-sm {
            padding: 5px 12px;
            font-size: 12px;
            border-radius: 7px;
        }

        .btn[disabled] {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* ─── Quick action buttons ─── */
        .quick-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .quick-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background: var(--card);
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            color: var(--ink);
            cursor: pointer;
            transition: all 0.18s;
            text-decoration: none;
            font-family: inherit;
        }

        .quick-btn:hover {
            border-color: var(--teal-mid);
            background: var(--teal-soft);
            color: var(--teal-dark);
        }

        .quick-btn iconify-icon {
            font-size: 18px;
        }

        /* ─── Forms ─── */
        label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 5px;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 9px 11px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 13.5px;
            font-family: inherit;
            background: #fff;
            color: var(--ink);
            transition: border-color 0.18s, box-shadow 0.18s;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #00b0b0;
            box-shadow: 0 0 0 3px rgba(0, 176, 176, 0.12);
        }

        .field {
            margin-bottom: 14px;
        }

        .form-row {
            display: flex;
            gap: 12px;
        }

        .form-row .field {
            flex: 1;
        }

        .form-hint {
            font-size: 11.5px;
            color: var(--slate);
            margin-top: 4px;
        }

        /* ─── Alerts ─── */
        .alert {
            padding: 12px 16px;
            border-radius: 9px;
            margin-bottom: 18px;
            font-size: 13.5px;
        }

        .alert-success {
            background: var(--teal-soft);
            color: var(--teal-dark);
            border-left: 3px solid var(--teal);
        }

        .alert-error {
            background: var(--red-soft);
            color: var(--red);
            border-left: 3px solid var(--red);
        }

        .empty-state {
            text-align: center;
            padding: 36px 20px;
            color: var(--slate);
            font-size: 13.5px;
        }

        /* ─── Timer banner ─── */
        .timer-banner {
            background: var(--amber-soft);
            border: 1px solid #ecd2a8;
            border-radius: var(--radius);
            border-left: 3px solid var(--amber);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .timer-banner .info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .timer-banner .pulse {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: var(--amber);
            animation: pulse 1.4s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.35;
            }
        }

        .timer-banner .clock {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 18px;
            font-weight: 600;
            color: var(--ink);
        }

        /* ─── Modals ─── */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(22, 48, 44, 0.45);
            z-index: 500;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-overlay.open {
            display: flex;
        }

        .modal-box {
            background: #fff;
            border-radius: 14px;
            padding: 26px;
            max-width: 460px;
            width: 100%;
            box-shadow: 0 16px 48px rgba(15, 31, 45, 0.18);
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-box h3 {
            margin: 0 0 16px;
            font-size: 16px;
            font-weight: 700;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 18px;
        }

        /* ─── Status filter tabs ─── */
        .filter-tabs {
            display: flex;
            gap: 4px;
            margin-bottom: 18px;
            border-bottom: 1.5px solid var(--border);
            padding-bottom: 0;
        }

        .filter-tab {
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            color: var(--slate);
            border: none;
            background: none;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            margin-bottom: -1.5px;
            font-family: inherit;
            transition: all 0.15s;
            text-decoration: none;
            display: inline-block;
        }

        .filter-tab:hover {
            color: var(--ink);
        }

        .filter-tab.active {
            color: var(--teal-dark);
            border-bottom-color: var(--teal);
        }

        /* ─── Utilities ─── */
        .text-muted {
            color: var(--slate);
        }

        .nowrap {
            white-space: nowrap;
        }

        .text-sm {
            font-size: 12px;
        }

        .mt-0 {
            margin-top: 0;
        }

        /* ─── Sidebar collapsed (desktop toggle) ─── */
        body.sidebar-mini #sidebar {
            transform: translateX(calc(-1 * var(--sidebar-w)));
        }

        body.sidebar-mini #main-container {
            margin-left: 0;
        }

        body.sidebar-mini .nb-header {
            left: 0;
        }

        /* ─── Tablet & mobile base (≤ 900px) ─── */
        @media (max-width: 900px) {
            #sidebar {
                transform: translateX(calc(-1 * var(--sidebar-w)));
                z-index: 300;
                /* above the backdrop */
            }

            body.sidebar-open::before {
                content: '';
                position: fixed;
                inset: 0;
                background: rgba(10, 20, 40, 0.45);
                z-index: 250;
                /* between sidebar and content */
                cursor: pointer;
            }

            body.sidebar-open #sidebar {
                transform: translateX(0);
            }

            #main-container {
                margin-left: 0;
            }

            .nb-header {
                left: 0;
            }

            /* Hide search bar — saves header space */
            .nb-header__search {
                display: none;
            }

            /* Show avatar only; hide name/role/chevron */
            .nb-header__profile-info,
            .nb-header__profile-chevron {
                display: none;
            }

            .page-content {
                padding: 20px 16px 48px;
            }

            .page-head {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .page-head>div:last-child {
                width: 100%;
                display: flex;
                gap: 8px;
                flex-wrap: wrap;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
            }

            .stat-row {
                gap: 10px;
            }

            .stat {
                min-width: 120px;
            }

            .grid-2 {
                grid-template-columns: 1fr;
            }

            .quick-actions {
                gap: 8px;
            }

            .quick-btn {
                padding: 9px 14px;
                font-size: 12.5px;
            }

            .timer-banner {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .card {
                padding: 16px;
            }

            /* Bottom sheet modal on mobile */
            .modal-overlay {
                padding: 12px;
                align-items: flex-end;
            }

            .modal-box {
                max-width: 100%;
                border-radius: 16px 16px 0 0;
                padding: 20px 18px;
                max-height: 85vh;
            }
        }

        /* ─── Small phones (≤ 480px) ─── */
        @media (max-width: 480px) {
            .stat-row {
                display: grid;
                grid-template-columns: 1fr 1fr;
            }

            .stat-grid {
                grid-template-columns: 1fr 1fr;
            }

            /* Tables scroll horizontally inside cards */
            .card {
                overflow-x: auto;
            }

            table {
                min-width: 480px;
            }

            .nb-header {
                padding: 0 14px;
            }

            .page-content {
                padding: 16px 12px 48px;
            }

            .page-head h1 {
                font-size: 18px;
            }

            /* Modal action buttons stack */
            .modal-actions {
                flex-direction: column-reverse;
            }

            .modal-actions .btn {
                width: 100%;
                justify-content: center;
            }

            /* Filter tabs scroll instead of wrapping */
            .filter-tabs {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
                flex-wrap: nowrap;
            }

            .filter-tabs::-webkit-scrollbar {
                display: none;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    @auth
        {{-- ── Sidebar ── --}}
        <div id="sidebar">
            <a href="{{ route('dashboard') }}" class="sidebar-brand">
                <img src="{{ asset('images/logo.png') }}" alt="Capili BPO" class="brand-logo">
            </a>

            <div class="sidebar-user">
                <div class="sidebar-user-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                    <div class="sidebar-user-role">{{ auth()->user()->role }}</div>
                </div>
            </div>

            <ul class="sidebar-nav">
                @if (auth()->user()->isAdmin())
                    <li class="sidebar-nav-header">Main</li>
                    <li>
                        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="bi bi-house"></i>
                            Overview
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.orgchart.index') }}"
                            class="{{ request()->routeIs('admin.orgchart.*') ? 'active' : '' }}">
                            <i class="bi bi-diagram-3"></i>
                            Org Chart
                        </a>
                    </li>

                    <li class="sidebar-nav-header">Management</li>
                    <li>
                        <a href="{{ route('admin.reports.index') }}"
                            class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                            <i class="bi bi-bar-chart-line"></i>
                            Reports
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.members.index') }}"
                            class="{{ request()->routeIs('admin.members.*') ? 'active' : '' }}">
                            <i class="bi bi-people"></i>
                            Members
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.facilities.index') }}"
                            class="{{ request()->routeIs('admin.facilities.*') ? 'active' : '' }}">
                            <i class="bi bi-building"></i>
                            Facilities
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.report-types.index') }}"
                            class="{{ request()->routeIs('admin.report-types.*') ? 'active' : '' }}">
                            <i class="bi bi-tag"></i>
                            Report Types
                        </a>
                    </li>

                    <li class="sidebar-nav-header">Leave</li>
                    <li>
                        <a href="{{ route('admin.leave-requests.index') }}"
                            class="{{ request()->routeIs('admin.leave-requests.*') ? 'active' : '' }}">
                            <i class="bi bi-calendar-check"></i>
                            Leave Requests
                            @php $pending = \App\Models\LeaveRequest::pending()->count(); @endphp
                            @if ($pending > 0)
                                <span class="nav-badge">{{ $pending }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.leave-types.index') }}"
                            class="{{ request()->routeIs('admin.leave-types.*') ? 'active' : '' }}">
                            <i class="bi bi-list-ul"></i>
                            Leave Types
                        </a>
                    </li>

                    <li class="sidebar-nav-header">Performance</li>
                    <li>
                        <a href="{{ route('admin.coaching.index') }}"
                            class="{{ request()->routeIs('admin.coaching.*') ? 'active' : '' }}">
                            <i class="bi bi-chat-left-text"></i>
                            Coaching Logs
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.pip.index') }}"
                            class="{{ request()->routeIs('admin.pip.*') ? 'active' : '' }}">
                            <i class="bi bi-exclamation-triangle"></i>
                            PIP Records
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.engagement.index') }}"
                            class="{{ request()->routeIs('admin.engagement.*') ? 'active' : '' }}">
                            <i class="bi bi-graph-up"></i>
                            Engagement & KPIs
                        </a>
                    </li>
                @else
                    <li class="sidebar-nav-header">Main</li>
                    <li>
                        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="bi bi-calendar3"></i>
                            My Day
                        </a>
                    </li>

                    <li class="sidebar-nav-header">Leave</li>
                    <li>
                        <a href="{{ route('member.leaves.index') }}"
                            class="{{ request()->routeIs('member.leaves.*') ? 'active' : '' }}">
                            <i class="bi bi-calendar-check"></i>
                            My Leaves
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('member.leaves.create') }}"
                            class="{{ request()->routeIs('member.leaves.create') ? 'active' : '' }}">
                            <i class="bi bi-plus-circle"></i>
                            File Leave
                        </a>
                    </li>
                @endif
            </ul>

            <div class="sidebar-logout">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit">
                        <i class="bi bi-box-arrow-right"></i>
                        Log out
                    </button>
                </form>
            </div>
        </div>

        {{-- ── Top Header ── --}}
        <nav class="nb-header">
            <div class="nb-header__left">
                <button class="nb-header__menu-btn" id="sidebarToggle" title="Toggle sidebar">
                    <iconify-icon icon="material-symbols:menu" width="22" height="22"></iconify-icon>
                </button>
                <div class="nb-header__search">
                    <iconify-icon class="nb-header__search-icon" icon="material-symbols:search" width="16"
                        height="16"></iconify-icon>
                    <input type="text" class="nb-header__search-input" id="memberSearch"
                        placeholder="Search members&hellip;">
                </div>
            </div>
            <div class="nb-header__right">
                <span class="nb-header__server-time" id="server-clock"></span>
                <div class="nb-header__profile">
                    <button class="nb-header__profile-btn" id="profileBtn">
                        <div class="nb-header__avatar-placeholder">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(strstr(auth()->user()->name, ' '), 1, 1)) }}
                        </div>
                        <div class="nb-header__profile-info">
                            <span class="nb-header__profile-name">{{ auth()->user()->name }}</span>
                            <span class="nb-header__profile-role">{{ ucfirst(auth()->user()->role) }}</span>
                        </div>
                        <iconify-icon icon="material-symbols:keyboard-arrow-down" width="16" height="16"
                            class="nb-header__profile-chevron"></iconify-icon>
                    </button>
                    <div class="nb-dropdown" id="profileMenu">
                        <a href="#" class="nb-dropdown__item nb-dropdown__item--logout"
                            onclick="event.preventDefault(); document.getElementById('header-logout-form').submit();">
                            <iconify-icon icon="material-symbols:logout" width="15" height="15"></iconify-icon>
                            Log out
                        </a>
                    </div>
                </div>
            </div>
        </nav>
        <form id="header-logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
    @endauth

    <div id="main-container">
        <div class="page-content">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-error">
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif
            @yield('content')
        </div>
    </div>

    <script>
        // Sidebar toggle
        const toggleBtn = document.getElementById('sidebarToggle');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                if (window.innerWidth <= 900) {
                    document.body.classList.toggle('sidebar-open');
                } else {
                    document.body.classList.toggle('sidebar-mini');
                }
            });
        }

        // Close sidebar when tapping the backdrop (mobile only)
        document.addEventListener('click', (e) => {
            if (
                document.body.classList.contains('sidebar-open') &&
                !e.target.closest('#sidebar') &&
                !e.target.closest('#sidebarToggle')
            ) {
                document.body.classList.remove('sidebar-open');
            }
        });

        // Clean up stale class when resizing between breakpoints
        window.addEventListener('resize', () => {
            if (window.innerWidth > 900) {
                document.body.classList.remove('sidebar-open');
            } else {
                document.body.classList.remove('sidebar-mini');
            }
        });

        // Profile dropdown
        const profileBtn = document.getElementById('profileBtn');
        const profileMenu = document.getElementById('profileMenu');
        if (profileBtn && profileMenu) {
            profileBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                profileMenu.classList.toggle('open');
            });
            document.addEventListener('click', () => profileMenu.classList.remove('open'));
        }

        // Live clock
        function updateClock() {
            const el = document.getElementById('server-clock');
            if (!el) return;
            const now = new Date();
            el.textContent = now.toLocaleTimeString('en-PH', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            });
        }
        updateClock();
        setInterval(updateClock, 1000);

        // Member name search
        const memberSearch = document.getElementById('memberSearch');
        if (memberSearch) {
            memberSearch.addEventListener('input', function() {
                const query = this.value.trim().toLowerCase();
                document.querySelectorAll('.page-content > .card').forEach(card => {
                    const nameEl = card.querySelector('h2');
                    if (!nameEl) return;
                    card.style.display = (!query || nameEl.textContent.trim().toLowerCase().includes(
                        query)) ? '' : 'none';
                });
            });
            memberSearch.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    this.value = '';
                    this.dispatchEvent(new Event('input'));
                    this.blur();
                }
            });
        }
    </script>
    @stack('scripts')
</body>

</html>
