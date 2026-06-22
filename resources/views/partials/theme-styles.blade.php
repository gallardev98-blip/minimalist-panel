@include('panel::partials.theme-variables')

<style>
    html,
    body {
        margin: 0;
        padding: 0;
    }

    .panel-body {
        background-color: rgb(var(--panel-bg));
        color: rgb(var(--panel-text));
        font-family: {!! \MyLaravelTools\Panel\Support\ThemeResolver::fontFamily() !!};
        min-height: 100vh;
        min-height: 100dvh;
    }

    .panel-heading { color: rgb(var(--panel-heading)); }
    .panel-text { color: rgb(var(--panel-text)); }
    .panel-muted { color: rgb(var(--panel-muted)); }

    .panel-card {
        background-color: rgb(var(--panel-card));
        border: 1px solid rgb(var(--panel-border));
        box-shadow: var(--panel-shadow);
    }

    .panel-surface { background-color: rgb(var(--panel-elevated)); }
    .panel-border { border-color: rgb(var(--panel-border)); }

    .panel-header {
        z-index: 30;
        border-bottom: 1px solid rgb(var(--panel-border));
        background-color: rgb(var(--panel-surface) / 0.85);
        backdrop-filter: blur(12px);
        height: var(--panel-header-height);
        min-height: var(--panel-header-height);
        max-height: var(--panel-header-height);
        display: flex;
        align-items: center;
        overflow: hidden;
    }

    .panel-header-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        width: 100%;
        height: 100%;
        padding: 0 1rem;
    }

    @media (min-width: 640px) {
        .panel-header-inner {
            padding: 0 1.5rem;
        }
    }

    @media (min-width: 1024px) {
        .panel-header-inner {
            padding: 0 2rem;
        }
    }

    .panel-shell {
        min-height: 100dvh;
        width: 100%;
    }

    @media (max-width: 1023px) {
        .panel-shell {
            display: flex;
            flex-direction: column;
            min-height: 100dvh;
        }

        .panel-main {
            flex: 1 1 auto;
        }
    }

    @media (min-width: 1024px) {
        .panel-shell {
            display: grid;
            grid-template-columns: var(--panel-sidebar-width) minmax(0, 1fr);
            grid-template-rows: minmax(0, 1fr);
            grid-template-areas: "sidebar main";
        }

        .panel-sidebar {
            grid-area: sidebar;
            position: relative;
            top: auto;
            bottom: auto;
            left: auto;
            transform: none !important;
            height: 100dvh;
            max-height: 100dvh;
        }

        .panel-main {
            grid-area: main;
        }
    }

    .panel-impersonation-sidebar {
        margin-bottom: 0.75rem;
    }

    .panel-impersonation-sidebar-card {
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        padding: 0.875rem;
        border-radius: 0.875rem;
        border: 1px solid rgb(var(--panel-warning) / 0.35);
        background: rgb(var(--panel-card));
        overflow: hidden;
        box-shadow:
            0 0 0 1px rgb(var(--panel-border) / 0.4),
            0 8px 24px rgb(var(--panel-warning) / 0.12),
            inset 0 1px 0 rgb(255 255 255 / 0.04);
    }

    .panel-impersonation-sidebar-glow {
        position: absolute;
        top: -2.5rem;
        right: -2rem;
        width: 7rem;
        height: 7rem;
        border-radius: 9999px;
        background: radial-gradient(circle, rgb(var(--panel-warning) / 0.35) 0%, transparent 70%);
        pointer-events: none;
        animation: panel-impersonation-glow 4s ease-in-out infinite;
    }

    .panel-impersonation-sidebar-shine {
        position: absolute;
        top: 0;
        left: -120%;
        width: 60%;
        height: 100%;
        background: linear-gradient(
            105deg,
            transparent 40%,
            rgb(255 255 255 / 0.07) 50%,
            transparent 60%
        );
        pointer-events: none;
        animation: panel-impersonation-shine 5s ease-in-out infinite;
    }

    @keyframes panel-impersonation-glow {
        0%, 100% { opacity: 0.55; transform: scale(1); }
        50% { opacity: 1; transform: scale(1.08); }
    }

    @keyframes panel-impersonation-shine {
        0%, 70% { left: -120%; }
        100% { left: 140%; }
    }

    .panel-impersonation-sidebar-body {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        min-width: 0;
    }

    .panel-impersonation-sidebar-avatar {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2.5rem;
        height: 2.5rem;
        flex-shrink: 0;
    }

    .panel-impersonation-sidebar-avatar-ring {
        position: absolute;
        inset: -2px;
        border-radius: 0.75rem;
        background: conic-gradient(
            from 0deg,
            rgb(var(--panel-warning)),
            rgb(var(--panel-warning) / 0.15),
            rgb(var(--panel-warning) / 0.5),
            rgb(var(--panel-warning))
        );
        animation: panel-impersonation-ring 3s linear infinite;
    }

    @keyframes panel-impersonation-ring {
        to { transform: rotate(360deg); }
    }

    .panel-impersonation-sidebar-avatar-letter {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        border-radius: 0.625rem;
        background: rgb(var(--panel-elevated));
        border: 1px solid rgb(var(--panel-border) / 0.6);
        font-size: 0.875rem;
        font-weight: 700;
        color: rgb(var(--panel-heading));
    }

    .panel-impersonation-sidebar-info {
        display: flex;
        flex-direction: column;
        gap: 0.125rem;
        min-width: 0;
        flex: 1 1 auto;
    }

    .panel-impersonation-sidebar-name {
        font-size: 0.8125rem;
        font-weight: 600;
        color: rgb(var(--panel-heading));
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .panel-impersonation-sidebar-email {
        font-size: 0.6875rem;
        color: rgb(var(--panel-muted));
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .panel-impersonation-sidebar-hint {
        font-size: 0.625rem;
        font-weight: 500;
        color: rgb(var(--panel-warning) / 0.85);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .panel-impersonation-sidebar-form {
        position: relative;
        z-index: 1;
        width: 100%;
    }

    .panel-impersonation-sidebar-exit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.375rem;
        width: 100%;
        height: 2.125rem;
        padding: 0 0.75rem;
        border-radius: 0.5rem;
        border: 1px solid rgb(var(--panel-warning) / 0.45);
        background: rgb(var(--panel-warning) / 0.08);
        color: rgb(var(--panel-heading));
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s ease, border-color 0.2s ease, transform 0.15s ease, box-shadow 0.2s ease;
    }

    .panel-impersonation-sidebar-exit:hover {
        background: rgb(var(--panel-warning) / 0.2);
        border-color: rgb(var(--panel-warning) / 0.65);
        box-shadow: 0 4px 14px rgb(var(--panel-warning) / 0.2);
        transform: translateY(-1px);
    }

    .panel-impersonation-sidebar-exit:active {
        transform: translateY(0);
    }

    .panel-sidebar {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        z-index: 50;
        width: var(--panel-sidebar-width);
        border-right: 1px solid rgb(var(--panel-border));
        background-color: rgb(var(--panel-surface));
        box-shadow: var(--panel-shadow);
    }

    .panel-sidebar-right .panel-sidebar {
        left: auto;
        right: 0;
        border-right: none;
        border-left: 1px solid rgb(var(--panel-border));
    }

    .panel-brand-mark {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        border-radius: 0.5rem;
        background: linear-gradient(135deg, rgb(var(--panel-primary)), rgb(var(--panel-accent)));
        color: rgb(var(--panel-primary-fg));
        box-shadow: 0 4px 12px rgb(var(--panel-primary) / 0.2);
        flex-shrink: 0;
    }

    .panel-brand-mark--custom {
        width: auto;
        height: auto;
        max-width: var(--panel-brand-logo-height, 2rem);
        max-height: var(--panel-brand-logo-height, 2rem);
        background: transparent;
        box-shadow: none;
        border-radius: 0.375rem;
        overflow: hidden;
    }

    .panel-brand-logo {
        display: block;
        max-width: var(--panel-brand-logo-height, 2rem);
        max-height: var(--panel-brand-logo-height, 2rem);
        width: auto;
        height: auto;
        object-fit: contain;
    }

    .panel-nav-static-item {
        text-decoration: none;
        cursor: pointer;
    }

    .panel-nav-static-item:hover {
        background-color: rgb(var(--panel-elevated));
    }

    .panel-nav-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: rgb(var(--panel-muted));
        transition: all 0.15s ease;
    }

    .panel-nav-link:hover {
        color: rgb(var(--panel-heading));
        background-color: rgb(var(--panel-elevated));
    }

    .panel-nav-link-active {
        color: rgb(var(--panel-primary));
        background-color: rgb(var(--panel-primary) / 0.1);
        box-shadow: inset 3px 0 0 0 rgb(var(--panel-primary));
    }

    .panel-nav-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        border-radius: 0.5rem;
        background-color: rgb(var(--panel-elevated));
        color: rgb(var(--panel-muted));
        transition: all 0.15s ease;
    }

    .panel-nav-link-active .panel-nav-icon {
        background-color: rgb(var(--panel-primary) / 0.2);
        color: rgb(var(--panel-primary));
    }

    .panel-nav-group {
        margin-top: 0.25rem;
    }

    .panel-nav-group-trigger {
        display: flex;
        width: 100%;
        align-items: center;
        justify-content: flex-start;
        gap: 0.75rem;
        padding: 0.5rem 0.75rem;
        border: none;
        border-radius: 0.5rem;
        background: transparent;
        font-size: 0.875rem;
        font-weight: 600;
        text-align: left;
        color: rgb(var(--panel-heading));
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .panel-nav-group-trigger:hover {
        background-color: rgb(var(--panel-elevated));
    }

    .panel-nav-group-trigger-open {
        color: rgb(var(--panel-heading));
    }

    .panel-nav-group-trigger-open .panel-nav-icon {
        color: rgb(var(--panel-heading));
        background-color: rgb(var(--panel-primary) / 0.12);
    }

    .panel-nav-group-chevron {
        color: rgb(var(--panel-muted));
    }

    .panel-nav-group-children {
        margin-top: 0.125rem;
        margin-bottom: 0.25rem;
        padding: 0.125rem 0 0.125rem 2.25rem;
        text-align: left;
    }

    .panel-nav-scroll {
        scrollbar-width: thin;
        scrollbar-color: rgb(var(--panel-border)) transparent;
    }

    .panel-nav-scroll::-webkit-scrollbar {
        width: 5px;
    }

    .panel-nav-scroll::-webkit-scrollbar-thumb {
        border-radius: 9999px;
        background-color: rgb(var(--panel-border));
    }

    .panel-nav-sublink {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        width: 100%;
        text-align: left;
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.8125rem;
        font-weight: 400;
        line-height: 1.25rem;
        color: rgb(var(--panel-muted));
        transition: color 0.15s ease, background-color 0.15s ease;
    }

    .panel-nav-sublink:hover {
        color: rgb(var(--panel-heading));
        background-color: rgb(var(--panel-elevated) / 0.6);
    }

    .panel-nav-sublink-active {
        font-weight: 500;
        color: rgb(var(--panel-heading));
        background-color: rgb(var(--panel-elevated) / 0.8);
    }

    .panel-nav-badge {
        border-radius: 9999px;
        background-color: rgb(var(--panel-elevated));
        padding: 0.125rem 0.5rem;
        font-size: 0.625rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: rgb(var(--panel-muted));
    }

    .panel-input {
        width: 100%;
        border: 1px solid rgb(var(--panel-input-border));
        background-color: rgb(var(--panel-input-bg));
        color: rgb(var(--panel-heading));
        border-radius: 0.5rem;
        padding: 0.5rem 0.875rem;
        font-size: 0.875rem;
        transition: border-color 0.15s, box-shadow 0.15s;
    }

    .panel-input::placeholder { color: rgb(var(--panel-muted)); }

    .panel-input:focus {
        outline: none;
        border-color: rgb(var(--panel-primary));
        box-shadow: 0 0 0 3px rgb(var(--panel-ring) / 0.2);
    }

    .panel-input-inline {
        width: auto;
    }

    .panel-search {
        position: relative;
        width: 100%;
        max-width: 28rem;
    }

    .panel-search-icon {
        position: absolute;
        left: 0.875rem;
        top: 50%;
        z-index: 1;
        height: 1rem;
        width: 1rem;
        transform: translateY(-50%);
        pointer-events: none;
        color: rgb(var(--panel-muted));
    }

    .panel-search-input {
        padding-left: 2.75rem;
    }

    .panel-filter-field {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .panel-filter-label {
        margin: 0;
        font-size: 0.6875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: rgb(var(--panel-muted));
        line-height: 1.2;
    }

    .panel-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.75rem;
    }

    .panel-toolbar-group {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem;
    }

    .panel-toolbar-selection {
        font-size: 0.8125rem;
        font-weight: 500;
        color: rgb(var(--panel-heading));
        white-space: nowrap;
    }

    .panel-toolbar-divider {
        width: 1px;
        height: 1.75rem;
        background-color: rgb(var(--panel-border));
    }

    .panel-export-group {
        display: inline-flex;
        align-items: stretch;
        border-radius: 0.5rem;
        border: 1px solid rgb(var(--panel-border));
        overflow: hidden;
    }

    .panel-export-group .panel-export-btn {
        border-radius: 0;
        border: none;
        border-right: 1px solid rgb(var(--panel-border));
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        padding: 0.4375rem 0.75rem;
    }

    .panel-export-group .panel-export-btn:last-child {
        border-right: none;
    }

    .panel-btn-compact {
        padding: 0.4375rem 0.75rem;
        font-size: 0.8125rem;
    }

    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }

    .panel-checkbox {
        border-radius: 0.25rem;
        border-color: rgb(var(--panel-input-border));
        background-color: rgb(var(--panel-input-bg));
        color: rgb(var(--panel-primary));
    }

    .panel-checkbox:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgb(var(--panel-ring) / 0.2);
    }

    .panel-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.15s ease;
        cursor: pointer;
        border: 1px solid transparent;
    }

    .panel-btn-primary {
        background: linear-gradient(135deg, rgb(var(--panel-primary)), rgb(var(--panel-primary-hover)));
        color: rgb(var(--panel-primary-fg));
        box-shadow: 0 2px 8px rgb(var(--panel-primary) / 0.25);
    }

    .panel-btn-primary:hover {
        filter: brightness(1.08);
        box-shadow: 0 4px 14px rgb(var(--panel-primary) / 0.45);
    }

    .panel-btn-secondary {
        border-color: rgb(var(--panel-border));
        background-color: rgb(var(--panel-card));
        color: rgb(var(--panel-text));
    }

    .panel-btn-secondary:hover {
        border-color: rgb(var(--panel-primary) / 0.4);
        color: rgb(var(--panel-primary));
        background-color: rgb(var(--panel-primary) / 0.05);
    }

    .panel-btn-ghost {
        color: rgb(var(--panel-muted));
        background: transparent;
    }

    .panel-btn-ghost:hover {
        color: rgb(var(--panel-heading));
        background-color: rgb(var(--panel-elevated));
    }

    .panel-btn-danger {
        color: rgb(var(--panel-danger));
        border-color: rgb(var(--panel-danger) / 0.3);
        background-color: rgb(var(--panel-danger) / 0.08);
    }

    .panel-btn-danger:hover {
        background-color: rgb(var(--panel-danger) / 0.15);
    }

    .panel-btn-success {
        color: rgb(var(--panel-success));
        border-color: rgb(var(--panel-success) / 0.3);
        background-color: rgb(var(--panel-success) / 0.08);
    }

    .panel-btn-success:hover {
        background-color: rgb(var(--panel-success) / 0.15);
    }

    .panel-btn-icon {
        padding: 0.5rem;
        border-radius: 0.5rem;
        border: 1px solid rgb(var(--panel-border));
        background-color: rgb(var(--panel-card));
        color: rgb(var(--panel-muted));
    }

    .panel-btn-icon:hover {
        color: rgb(var(--panel-heading));
        border-color: rgb(var(--panel-primary) / 0.3);
    }

    .panel-table-wrap {
        overflow: hidden;
        border-radius: var(--panel-radius);
        border: 1px solid rgb(var(--panel-border));
        background-color: rgb(var(--panel-card));
    }

    .panel-table { width: 100%; border-collapse: collapse; }

    .panel-table thead {
        background-color: rgb(var(--panel-elevated));
    }

    .panel-table th {
        padding: 0.75rem 1rem;
        text-align: left;
        font-size: 0.6875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: rgb(var(--panel-muted));
    }

    .panel-table td {
        padding: 0.875rem 1rem;
        font-size: 0.875rem;
        color: rgb(var(--panel-text));
        border-top: 1px solid rgb(var(--panel-border));
    }

    .panel-table tbody tr {
        transition: background-color 0.1s;
    }

    .panel-table tbody tr:hover {
        background-color: rgb(var(--panel-primary) / 0.03);
    }

    .panel-table-sort:hover { color: rgb(var(--panel-heading)); }

    .panel-table-actions-col {
        width: 3.5rem;
        text-align: center;
        vertical-align: middle;
    }

    .panel-row-actions {
        position: relative;
        display: inline-flex;
        justify-content: center;
    }

    .panel-row-actions-trigger {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        border-radius: 0.375rem;
        border: 1px solid transparent;
        color: rgb(var(--panel-muted));
        background: transparent;
        cursor: pointer;
        transition: color 0.15s, background-color 0.15s, border-color 0.15s;
    }

    .panel-row-actions-trigger:hover,
    .panel-row-actions-trigger[aria-expanded="true"] {
        color: rgb(var(--panel-heading));
        background-color: rgb(var(--panel-elevated));
        border-color: rgb(var(--panel-border));
    }

    .panel-row-actions-menu {
        position: absolute;
        right: 0;
        top: calc(100% + 0.25rem);
        z-index: 30;
        min-width: 9rem;
        padding: 0.25rem;
        border-radius: 0.5rem;
        border: 1px solid rgb(var(--panel-border));
        background-color: rgb(var(--panel-card));
        box-shadow: var(--panel-shadow-lg);
    }

    .panel-row-actions-item {
        display: flex;
        width: 100%;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0.625rem;
        border-radius: 0.375rem;
        font-size: 0.8125rem;
        color: rgb(var(--panel-text));
        text-align: left;
        background: transparent;
        border: none;
        cursor: pointer;
        transition: background-color 0.1s, color 0.1s;
    }

    .panel-row-actions-item:hover {
        background-color: rgb(var(--panel-elevated));
        color: rgb(var(--panel-heading));
    }

    .panel-row-actions-item-primary { color: rgb(var(--panel-primary)); }
    .panel-row-actions-item-danger { color: rgb(var(--panel-danger)); }
    .panel-row-actions-item-success { color: rgb(var(--panel-success)); }

    .panel-action-link {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.8125rem;
        color: rgb(var(--panel-muted));
        transition: all 0.1s;
    }

    .panel-action-link:hover {
        color: rgb(var(--panel-heading));
        background-color: rgb(var(--panel-elevated));
    }

    .panel-action-primary { color: rgb(var(--panel-primary)); }
    .panel-action-primary:hover { background-color: rgb(var(--panel-primary) / 0.1); }
    .panel-action-danger { color: rgb(var(--panel-danger)); }
    .panel-action-danger:hover { background-color: rgb(var(--panel-danger) / 0.1); }
    .panel-action-success { color: rgb(var(--panel-success)); }
    .panel-action-success:hover { background-color: rgb(var(--panel-success) / 0.1); }

    .panel-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 9999px;
        padding: 0.125rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .panel-badge-success { background-color: rgb(var(--panel-success) / 0.15); color: rgb(52 211 153); }
    .panel-badge-danger { background-color: rgb(var(--panel-danger) / 0.15); color: rgb(251 113 133); }
    .panel-badge-warning { background-color: rgb(var(--panel-warning) / 0.15); color: rgb(251 191 36); }
    .panel-badge-primary { background-color: rgb(var(--panel-primary) / 0.15); color: rgb(var(--panel-primary)); }
    .panel-badge-muted { background-color: rgb(var(--panel-elevated)); color: rgb(var(--panel-muted)); }

    .panel-widget-card {
        position: relative;
        overflow: hidden;
        border-radius: var(--panel-radius);
        border: 1px solid rgb(var(--panel-border));
        background-color: rgb(var(--panel-card));
        padding: 1.25rem;
        transition: all 0.2s ease;
    }

    .panel-widget-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, rgb(var(--panel-primary)), rgb(var(--panel-accent)));
        opacity: 0;
        transition: opacity 0.2s;
    }

    .panel-widget-card:hover {
        border-color: rgb(var(--panel-primary) / 0.3);
        box-shadow: var(--panel-shadow-lg);
        transform: translateY(-2px);
    }

    .panel-widget-card:hover::before { opacity: 1; }

    .panel-resource-card {
        border-radius: var(--panel-radius);
        border: 1px solid rgb(var(--panel-border));
        background-color: rgb(var(--panel-card));
        padding: 1.25rem;
        transition: all 0.2s ease;
    }

    .panel-resource-card:hover {
        border-color: rgb(var(--panel-primary) / 0.4);
        box-shadow: var(--panel-shadow-lg);
        transform: translateY(-2px);
    }

    .panel-resource-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.625rem;
        background: linear-gradient(135deg, rgb(var(--panel-primary) / 0.12), rgb(var(--panel-accent) / 0.08));
        color: rgb(var(--panel-primary));
    }

    .panel-bulk-bar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem;
        border-radius: 0.5rem;
        border: 1px solid rgb(var(--panel-border));
        background-color: rgb(var(--panel-elevated) / 0.6);
        padding: 0.75rem 1rem;
    }

    .panel-toast {
        pointer-events: auto;
        border-radius: 0.5rem;
        border: 1px solid;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        box-shadow: var(--panel-shadow-lg);
        backdrop-filter: blur(8px);
    }

    .panel-toast-success {
        border-color: rgb(var(--panel-success) / 0.3);
        background-color: rgb(var(--panel-success) / 0.12);
        color: rgb(52 211 153);
    }

    .panel-toast-error {
        border-color: rgb(var(--panel-danger) / 0.3);
        background-color: rgb(var(--panel-danger) / 0.12);
        color: rgb(251 113 133);
    }

    .panel-toast-info {
        border-color: rgb(var(--panel-border));
        background-color: rgb(var(--panel-card) / 0.95);
        color: rgb(var(--panel-text));
    }

    .panel-pagination-btn {
        padding: 0.375rem 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        color: rgb(var(--panel-muted));
        transition: all 0.1s;
    }

    .panel-pagination-btn:hover:not(:disabled) {
        color: rgb(var(--panel-heading));
        background-color: rgb(var(--panel-elevated));
    }

    .panel-pagination-btn-active {
        background: linear-gradient(135deg, rgb(var(--panel-primary)), rgb(var(--panel-primary-hover)));
        color: rgb(var(--panel-primary-fg));
        font-weight: 500;
    }

    .panel-pagination-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    .panel-user-avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        border-radius: 9999px;
        background: linear-gradient(135deg, rgb(var(--panel-primary) / 0.15), rgb(var(--panel-accent) / 0.1));
        color: rgb(var(--panel-primary));
        font-size: 0.75rem;
        font-weight: 600;
        flex-shrink: 0;
    }

    .panel-profile-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        width: 100%;
        padding: 0.625rem 0.75rem;
        border-radius: calc(var(--panel-radius) - 0.125rem);
        border: 1px solid rgb(var(--panel-border));
        background-color: rgb(var(--panel-elevated) / 0.35);
        text-decoration: none;
        transition: border-color 0.15s ease, background-color 0.15s ease;
    }

    .panel-profile-link:hover {
        border-color: rgb(var(--panel-muted) / 0.45);
        background-color: rgb(var(--panel-elevated) / 0.65);
    }

    .panel-profile-link-icon {
        color: rgb(var(--panel-muted));
    }

    .panel-header-user-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        border-radius: 9999px;
        padding: 0.125rem 0.375rem 0.125rem 0.125rem;
        transition: background-color 0.15s ease;
    }

    .panel-header-user-link:hover {
        background-color: rgb(var(--panel-elevated) / 0.65);
    }

    .panel-profile-section-title {
        font-size: 0.875rem;
        font-weight: 600;
        letter-spacing: -0.01em;
        color: rgb(var(--panel-heading));
    }

    .panel-page-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .panel-page-header-start {
        flex: 1 1 auto;
        min-width: 0;
    }

    .panel-page-header-start h1,
    .panel-page-hero h1 {
        font-size: 1.5rem;
        font-weight: 700;
        letter-spacing: -0.025em;
        color: rgb(var(--panel-heading));
    }

    .panel-page-header .panel-breadcrumbs {
        flex-shrink: 0;
        align-self: center;
        max-width: min(50%, 24rem);
        margin-left: auto;
    }

    .panel-page-header .panel-breadcrumbs-list {
        justify-content: flex-end;
    }

    .panel-page-hero {
        margin-bottom: 2rem;
    }

    .panel-form-card {
        max-width: 42rem;
        border-radius: var(--panel-radius);
        border: 1px solid rgb(var(--panel-border));
        background-color: rgb(var(--panel-card));
        overflow: hidden;
    }

    .panel-form-card-body {
        padding: 1.5rem;
    }

    .panel-form-footer {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.5rem;
        border-top: 1px solid rgb(var(--panel-border));
        background-color: rgb(var(--panel-elevated) / 0.4);
    }

    .panel-detail-card {
        border-radius: var(--panel-radius);
        border: 1px solid rgb(var(--panel-border));
        background-color: rgb(var(--panel-card));
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .panel-detail-list {
        margin: 0;
    }

    .panel-detail-row {
        display: grid;
        gap: 0.5rem 1rem;
        padding: 1rem 1.5rem;
        border-top: 1px solid rgb(var(--panel-border));
    }

    .panel-detail-row:first-child {
        border-top: none;
    }

    @media (min-width: 640px) {
        .panel-detail-row {
            grid-template-columns: minmax(8rem, 1fr) 2fr;
            align-items: start;
        }
    }

    .panel-detail-label {
        margin: 0;
        font-size: 0.8125rem;
        font-weight: 500;
        color: rgb(var(--panel-muted));
    }

    .panel-detail-value {
        margin: 0;
        font-size: 0.875rem;
        color: rgb(var(--panel-text));
    }

    .panel-relation-section {
        margin-top: 2rem;
    }

    .panel-relation-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .panel-relation-title {
        margin: 0;
        font-size: 1.0625rem;
        font-weight: 600;
        color: rgb(var(--panel-heading));
    }

    .panel-relation-form {
        margin-bottom: 1rem;
        max-width: none;
    }

    .panel-section-title {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: rgb(var(--panel-muted));
    }

    .panel-label {
        display: block;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: rgb(var(--panel-text));
    }

    .panel-back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.875rem;
        color: rgb(var(--panel-muted));
        transition: color 0.15s;
    }

    .panel-back-link:hover { color: rgb(var(--panel-heading)); }

    .panel-overlay {
        background-color: rgb(0 0 0 / 0.5);
    }

    .dark .panel-overlay {
        background-color: rgb(0 0 0 / 0.7);
    }

    .panel-empty-state {
        border: 2px dashed rgb(var(--panel-border));
        border-radius: var(--panel-radius);
        padding: 2.5rem;
        text-align: center;
    }

    .panel-section-title {
        margin-bottom: 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: rgb(var(--panel-muted));
    }

    .panel-breadcrumbs ol,
    .panel-breadcrumbs-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .panel-breadcrumbs-list {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.375rem;
    }

    .panel-breadcrumbs-item {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        min-width: 0;
        max-width: 100%;
    }

    .panel-breadcrumbs-separator {
        font-size: 0.875rem;
        user-select: none;
    }

    .panel-breadcrumbs-link {
        font-size: 0.875rem;
        text-decoration: none;
        transition: color 0.15s ease;
    }

    .panel-breadcrumbs-link:hover {
        color: rgb(var(--panel-text));
    }

    .panel-breadcrumbs-current {
        font-size: 0.875rem;
        font-weight: 500;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .panel-sidebar-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        margin-top: 0.75rem;
    }

    .panel-sidebar-version {
        flex: 1 1 auto;
        text-align: center;
        font-size: 0.6875rem;
        font-weight: 500;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        user-select: none;
    }

    .panel-sidebar-toolbar-btn {
        flex-shrink: 0;
    }

    .panel-sidebar-logout-form {
        margin: 0;
        flex-shrink: 0;
    }

    .panel-sidebar-logout-btn:hover {
        color: rgb(var(--panel-danger));
    }

    .panel-form-section {
        border: 1px solid rgb(var(--panel-border));
        border-radius: var(--panel-radius);
        overflow: hidden;
    }

    .panel-form-section + .panel-form-section,
    .panel-form-section + div,
    div + .panel-form-section {
        margin-top: 0.25rem;
    }

    .panel-form-section-header {
        padding: 1rem 1.25rem;
        background-color: rgb(var(--panel-elevated));
        border-bottom: 1px solid rgb(var(--panel-border));
    }

    .panel-form-section-title {
        margin: 0;
        font-size: 0.9375rem;
        font-weight: 600;
        color: rgb(var(--panel-heading));
    }

    .panel-form-section-description {
        margin: 0.25rem 0 0;
        font-size: 0.8125rem;
        color: rgb(var(--panel-muted));
    }

    .panel-form-section-body {
        padding: 1.25rem;
    }

    .panel-form-tabs {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .panel-form-tabs-nav {
        display: flex;
        flex-wrap: wrap;
        gap: 0.25rem;
        padding-bottom: 0.25rem;
        border-bottom: 1px solid rgb(var(--panel-border));
    }

    .panel-form-tab {
        padding: 0.5rem 0.875rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: rgb(var(--panel-muted));
        background: transparent;
        border: none;
        border-radius: calc(var(--panel-radius) - 0.125rem);
        cursor: pointer;
        transition: color 0.15s, background-color 0.15s;
    }

    .panel-form-tab:hover {
        color: rgb(var(--panel-heading));
        background-color: rgb(var(--panel-elevated));
    }

    .panel-form-tab--active {
        color: rgb(var(--panel-heading));
        background-color: rgb(var(--panel-elevated));
        box-shadow: inset 0 -2px 0 rgb(var(--panel-primary));
    }

    .panel-form-tab-panel {
        min-height: 0;
    }


    .panel-main {
        position: relative;
        min-height: 0;
        flex: 1 1 auto;
    }

    .panel-main.panel-navigating .panel-main-content {
        pointer-events: none;
    }

    html.panel-scroll-lock,
    body.panel-scroll-lock {
        overflow: hidden !important;
        overscroll-behavior: none;
    }

    .panel-modal-root {
        position: fixed;
        inset: 0;
        z-index: 60;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem 1rem 1.5rem;
    }

    .panel-modal-backdrop {
        position: fixed;
        inset: 0;
        background-color: rgb(0 0 0 / 0.5);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
    }

    .panel-modal-dialog {
        position: relative;
        z-index: 1;
        display: flex;
        width: 100%;
        max-width: 42rem;
        max-height: min(90vh, 48rem);
        flex-direction: column;
        overflow: hidden;
        box-shadow: var(--panel-shadow-lg);
    }

    .panel-modal-dialog--compact {
        max-width: 28rem;
        padding: 1.5rem;
    }

    .panel-modal-dialog--wide {
        max-width: 56rem;
    }

    .panel-import-preview-table-wrap {
        max-height: 20rem;
        overflow: auto;
        border: 1px solid rgb(var(--panel-border));
        border-radius: var(--panel-radius);
    }

    .panel-import-preview-table {
        margin: 0;
    }

    .panel-import-preview-row--invalid {
        background-color: rgb(var(--panel-danger) / 0.06);
    }

    .panel-modal-header {
        display: flex;
        flex-shrink: 0;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid rgb(var(--panel-border));
    }

    .panel-modal-body {
        min-height: 0;
        flex: 1 1 auto;
        overflow-y: auto;
        padding: 1.25rem 1.5rem;
    }

    .panel-modal-footer {
        display: flex;
        flex-shrink: 0;
        align-items: center;
        justify-content: flex-end;
        gap: 0.5rem;
        padding: 1rem 1.5rem;
        border-top: 1px solid rgb(var(--panel-border));
    }

    .panel-modal-form {
        display: flex;
        min-height: 0;
        flex: 1 1 auto;
        flex-direction: column;
    }

    .panel-modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
        margin-top: 1.5rem;
    }

    /* Livewire Navigate (NProgress): oculto — el panel usa panel-spa-loader */
    #nprogress,
    #nprogress .bar,
    #nprogress .spinner {
        display: none !important;
    }

    .panel-spa-loader {
        --panel-loader-exit: 360ms;
        position: fixed;
        z-index: 30;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
        opacity: 0;
        visibility: hidden;
        transition:
            opacity var(--panel-loader-exit) cubic-bezier(0.4, 0, 0.2, 1),
            visibility var(--panel-loader-exit) cubic-bezier(0.4, 0, 0.2, 1);
    }

    body.panel-layout-sidebar .panel-spa-loader:not(.panel-spa-loader--fullscreen) {
        inset: 0;
    }

    body.panel-layout-topbar .panel-spa-loader:not(.panel-spa-loader--fullscreen),
    body.panel-layout-dual .panel-spa-loader:not(.panel-spa-loader--fullscreen) {
        inset: var(--panel-header-height) 0 0 0;
    }

    @media (min-width: 1024px) {
        body.panel-layout-sidebar .panel-spa-loader:not(.panel-spa-loader--fullscreen) {
            left: var(--panel-sidebar-width);
        }

        html.panel-sidebar-collapsed body.panel-layout-sidebar .panel-spa-loader:not(.panel-spa-loader--fullscreen) {
            left: var(--panel-sidebar-collapsed-width);
        }

        body.panel-layout-dual .panel-spa-loader:not(.panel-spa-loader--fullscreen) {
            left: var(--panel-sidebar-width);
        }

        html.panel-sidebar-collapsed body.panel-layout-dual .panel-spa-loader:not(.panel-spa-loader--fullscreen) {
            left: var(--panel-sidebar-collapsed-width);
        }
    }

    .panel-spa-loader--fullscreen,
    body.panel-auth-body .panel-spa-loader {
        inset: 0;
        z-index: 35;
    }

    .panel-spa-loader--visible {
        opacity: 1;
        visibility: visible;
        pointer-events: all;
    }

    .panel-spa-loader-backdrop {
        position: absolute;
        inset: 0;
        background-color: rgb(var(--panel-bg) / 0.45);
        backdrop-filter: blur(16px) saturate(120%);
        -webkit-backdrop-filter: blur(16px) saturate(120%);
        opacity: 0;
        transition: opacity var(--panel-loader-exit) cubic-bezier(0.4, 0, 0.2, 1);
    }

    .panel-spa-loader--visible .panel-spa-loader-backdrop {
        opacity: 1;
    }

    .dark .panel-spa-loader-backdrop {
        background-color: rgb(var(--panel-bg) / 0.62);
    }

    .panel-spa-loader-content {
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        text-align: center;
        transform: translateY(8px);
        opacity: 0;
        transition:
            transform var(--panel-loader-exit) cubic-bezier(0.34, 1.2, 0.64, 1),
            opacity calc(var(--panel-loader-exit) - 40ms) cubic-bezier(0.4, 0, 0.2, 1);
    }

    .panel-spa-loader--visible .panel-spa-loader-content {
        transform: translateY(0);
        opacity: 1;
    }

    .panel-spa-loader-ring {
        position: relative;
        width: 4rem;
        height: 4rem;
        --panel-loader-progress: 0;
    }

    .panel-spa-loader-ring-track,
    .panel-spa-loader-ring-progress {
        position: absolute;
        inset: 0;
        border-radius: 9999px;
    }

    .panel-spa-loader-ring-track {
        border: 2px solid rgb(var(--panel-border));
        opacity: 0.65;
    }

    .panel-spa-loader-ring-progress {
        background: conic-gradient(
            rgb(var(--panel-primary)) calc(var(--panel-loader-progress, 0) * 1%),
            transparent 0
        );
        -webkit-mask: radial-gradient(farthest-side, transparent calc(100% - 3px), #000 calc(100% - 2px));
        mask: radial-gradient(farthest-side, transparent calc(100% - 3px), #000 calc(100% - 2px));
        transition: background 120ms linear;
    }

    .panel-spa-loader-percent {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.6875rem;
        font-weight: 700;
        letter-spacing: -0.02em;
        font-variant-numeric: tabular-nums;
        color: rgb(var(--panel-heading));
        pointer-events: none;
        user-select: none;
    }

    .panel-spa-loader-title {
        margin: 0;
        font-size: 0.9375rem;
        font-weight: 600;
        letter-spacing: -0.01em;
        color: rgb(var(--panel-heading));
    }

    .panel-skeleton {
        background: linear-gradient(
            90deg,
            rgb(var(--panel-elevated)) 25%,
            rgb(var(--panel-border) / 0.5) 50%,
            rgb(var(--panel-elevated)) 75%
        );
        background-size: 200% 100%;
        animation: panel-skeleton 1.2s ease-in-out infinite;
    }

    @keyframes panel-skeleton {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    .panel-auth-body.panel-body {
        min-height: 0;
        height: 100dvh;
        max-height: 100dvh;
        overflow: hidden;
        overscroll-behavior: none;
        background-color: rgb(var(--panel-bg));
        color: rgb(var(--panel-text));
        position: relative;
    }

    .panel-auth-bg {
        pointer-events: none;
        position: fixed;
        inset: 0;
        z-index: 0;
        background-color: rgb(var(--panel-bg));
        background-image:
            radial-gradient(rgb(var(--panel-border) / 0.35) 1px, transparent 1px),
            radial-gradient(ellipse 80% 60% at 50% -10%, rgb(var(--panel-primary) / 0.06), transparent 55%),
            radial-gradient(ellipse 60% 40% at 100% 100%, rgb(var(--panel-primary) / 0.04), transparent 50%);
        background-size: 24px 24px, auto, auto;
        background-position: center center;
    }

    .dark .panel-auth-bg {
        background-image:
            radial-gradient(rgb(var(--panel-border) / 0.5) 1px, transparent 1px),
            radial-gradient(ellipse 80% 60% at 50% -10%, rgb(var(--panel-primary) / 0.08), transparent 55%),
            radial-gradient(ellipse 60% 40% at 0% 100%, rgb(var(--panel-primary) / 0.05), transparent 50%);
        background-size: 24px 24px, auto, auto;
    }

    .panel-auth-theme-toggle {
        position: fixed;
        top: max(1rem, env(safe-area-inset-top));
        right: max(1rem, env(safe-area-inset-right));
        z-index: 20;
        border: 1px solid rgb(var(--panel-border));
        background-color: rgb(var(--panel-card) / 0.85);
        backdrop-filter: blur(8px);
    }

    .panel-auth-shell {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
        height: 100%;
        max-height: 100dvh;
        overflow: hidden;
        padding:
            max(0.75rem, env(safe-area-inset-top))
            max(0.75rem, env(safe-area-inset-right))
            max(0.75rem, env(safe-area-inset-bottom))
            max(0.75rem, env(safe-area-inset-left));
    }

    .panel-auth-card {
        display: flex;
        flex-direction: column;
        width: 100%;
        max-width: 26rem;
        max-height: 100%;
        overflow: hidden;
        border: 1px solid rgb(var(--panel-border));
        border-radius: calc(var(--panel-radius, 0.75rem) + 0.25rem);
        background-color: rgb(var(--panel-card) / 0.92);
        backdrop-filter: blur(12px);
        box-shadow:
            0 0 0 1px rgb(var(--panel-border) / 0.4),
            0 24px 48px -12px rgb(0 0 0 / 0.12);
    }

    .dark .panel-auth-card {
        box-shadow:
            0 0 0 1px rgb(var(--panel-border) / 0.6),
            0 32px 64px -16px rgb(0 0 0 / 0.55);
    }

    .panel-auth-brand {
        flex-shrink: 0;
        display: flex;
        justify-content: center;
        padding: 1.5rem 1.5rem 0;
    }

    .panel-auth-brand-link {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        color: inherit;
        transition: opacity 0.15s ease;
    }

    .panel-auth-brand-link:hover {
        opacity: 0.85;
    }

    .panel-auth-brand-mark .panel-brand-mark {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.75rem;
    }

    .panel-auth-brand-mark .panel-brand-mark svg {
        width: 1.125rem;
        height: 1.125rem;
    }

    .panel-auth-brand-name {
        font-size: 0.625rem;
        font-weight: 600;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: rgb(var(--panel-muted));
    }

    .panel-auth-card-body {
        flex: 1;
        min-height: 0;
        overflow: hidden;
        padding: 1.25rem 1.5rem 1.5rem;
    }

    @media (min-width: 640px) {
        .panel-auth-card-body {
            padding: 1.375rem 1.75rem 1.75rem;
        }

        .panel-auth-brand {
            padding-top: 1.75rem;
        }
    }

    .panel-auth-form {
        display: flex;
        flex-direction: column;
        align-items: stretch;
        height: 100%;
        min-height: 0;
        text-align: center;
    }

    .panel-auth-intro {
        flex-shrink: 0;
        margin-bottom: 1.25rem;
    }

    .panel-auth-title {
        font-size: 1.375rem;
        font-weight: 700;
        letter-spacing: -0.025em;
        line-height: 1.2;
        color: rgb(var(--panel-heading));
    }

    .panel-auth-subtitle {
        margin-top: 0.375rem;
        font-size: 0.8125rem;
        line-height: 1.4;
        color: rgb(var(--panel-muted));
    }

    .panel-auth-fields {
        display: flex;
        flex: 1;
        flex-direction: column;
        gap: 0.875rem;
        min-height: 0;
        text-align: left;
        width: 100%;
    }

    .panel-auth-form--register .panel-auth-fields {
        gap: 0.625rem;
    }

    .panel-auth-form--register .panel-auth-intro {
        margin-bottom: 1rem;
    }

    .panel-auth-field {
        display: flex;
        flex-shrink: 0;
        flex-direction: column;
        gap: 0.375rem;
    }

    .panel-auth-label {
        font-size: 0.625rem;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: rgb(var(--panel-muted));
    }

    .panel-auth-input-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }

    .panel-auth-input-icon {
        pointer-events: none;
        position: absolute;
        left: 0.75rem;
        display: flex;
        color: rgb(var(--panel-muted));
        transition: color 0.15s ease;
    }

    .panel-auth-input {
        width: 100%;
        height: 2.625rem;
        border: 1px solid rgb(var(--panel-input-border));
        border-radius: 0.625rem;
        background-color: rgb(var(--panel-input-bg));
        padding: 0 0.75rem 0 2.375rem;
        font-size: 0.875rem;
        color: rgb(var(--panel-heading));
        transition: border-color 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
    }

    .panel-auth-form--register .panel-auth-input {
        height: 2.375rem;
        font-size: 0.8125rem;
    }

    .panel-auth-input::placeholder {
        color: rgb(var(--panel-muted));
    }

    .panel-auth-input:hover {
        border-color: rgb(var(--panel-muted) / 0.5);
    }

    .panel-auth-input:focus {
        outline: none;
        border-color: rgb(var(--panel-primary));
        box-shadow: 0 0 0 3px rgb(var(--panel-ring) / 0.18);
        background-color: rgb(var(--panel-input-bg));
    }

    .panel-auth-input:-webkit-autofill,
    .panel-auth-input:-webkit-autofill:hover,
    .panel-auth-input:-webkit-autofill:focus,
    .panel-auth-input:-webkit-autofill:active {
        -webkit-text-fill-color: rgb(var(--panel-heading)) !important;
        caret-color: rgb(var(--panel-heading));
        box-shadow: 0 0 0 1000px rgb(var(--panel-input-bg)) inset !important;
        border-color: rgb(var(--panel-input-border));
        transition: background-color 99999s ease-out 0s;
    }

    .panel-auth-input:-webkit-autofill:focus {
        border-color: rgb(var(--panel-primary));
        box-shadow:
            0 0 0 1000px rgb(var(--panel-input-bg)) inset,
            0 0 0 3px rgb(var(--panel-ring) / 0.18) !important;
    }

    .panel-auth-input:autofill {
        box-shadow: 0 0 0 1000px rgb(var(--panel-input-bg)) inset;
        -webkit-text-fill-color: rgb(var(--panel-heading));
        caret-color: rgb(var(--panel-heading));
    }

    .panel-auth-input-wrap:focus-within .panel-auth-input-icon {
        color: rgb(var(--panel-text));
    }

    .panel-auth-error {
        font-size: 0.75rem;
        line-height: 1.3;
        color: rgb(var(--panel-danger));
    }

    .panel-auth-error-summary {
        padding: 0.75rem 0.875rem;
        border-radius: calc(var(--panel-radius) - 0.125rem);
        border: 1px solid rgb(var(--panel-danger) / 0.35);
        background-color: rgb(var(--panel-danger) / 0.08);
    }

    .panel-auth-error-summary p {
        margin: 0;
    }

    .panel-auth-error-summary p + p {
        margin-top: 0.25rem;
    }

    .panel-auth-remember {
        display: flex;
        flex-shrink: 0;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        font-size: 0.8125rem;
        color: rgb(var(--panel-muted));
        user-select: none;
    }

    .panel-auth-checkbox {
        width: 0.875rem;
        height: 0.875rem;
        flex-shrink: 0;
    }

    .panel-auth-submit {
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 2.625rem;
        margin-top: 0.125rem;
        border-radius: 0.625rem;
        font-size: 0.875rem;
        font-weight: 600;
        letter-spacing: 0.01em;
        white-space: nowrap;
    }

    .panel-auth-submit-loading {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }

    .panel-auth-loading-text {
        display: inline-flex;
        align-items: baseline;
    }

    .panel-auth-dots {
        display: inline-block;
        width: 1.125em;
        text-align: left;
    }

    .panel-auth-dots::after {
        content: '';
        animation: panel-auth-dots 1.2s steps(4, end) infinite;
    }

    @keyframes panel-auth-dots {
        0% { content: ''; }
        25% { content: '.'; }
        50% { content: '..'; }
        75%, 100% { content: '...'; }
    }

    .panel-auth-form--register .panel-auth-submit {
        height: 2.375rem;
        font-size: 0.8125rem;
    }

    .panel-auth-submit--loading {
        opacity: 0.85;
        cursor: wait;
    }

    .panel-auth-forgot {
        display: flex;
        justify-content: flex-end;
        margin-top: -0.25rem;
    }

    .panel-auth-forgot-link {
        font-size: 0.8125rem;
        font-weight: 500;
        color: rgb(var(--panel-muted));
        text-decoration: none;
        transition: color 0.15s ease;
    }

    .panel-auth-forgot-link:hover {
        color: rgb(var(--panel-heading));
    }

    .panel-auth-status {
        margin-bottom: 1rem;
        padding: 0.75rem 1rem;
        border-radius: var(--panel-radius, 0.75rem);
        border: 1px solid rgb(var(--panel-success) / 0.35);
        background: rgb(var(--panel-success) / 0.08);
        color: rgb(var(--panel-heading));
        font-size: 0.875rem;
        line-height: 1.45;
    }

    .panel-auth-footer {
        display: flex;
        flex-shrink: 0;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        gap: 0.25rem 0.5rem;
        margin-top: 1.25rem;
        padding-top: 1.125rem;
        border-top: 1px solid rgb(var(--panel-border));
        font-size: 0.8125rem;
    }

    .panel-auth-form--register .panel-auth-footer {
        margin-top: 1rem;
        padding-top: 1rem;
    }

    .panel-auth-footer-text {
        color: rgb(var(--panel-muted));
    }

    .panel-auth-footer-link {
        font-weight: 600;
        color: rgb(var(--panel-heading));
        text-decoration: none;
        border-bottom: 1px solid rgb(var(--panel-heading) / 0.35);
        transition: border-color 0.15s ease, opacity 0.15s ease;
    }

    .panel-auth-footer-link:hover {
        border-bottom-color: rgb(var(--panel-heading));
        opacity: 0.9;
    }

    @media (max-height: 720px) {
        .panel-auth-brand {
            padding-top: 1rem;
        }

        .panel-auth-brand-mark .panel-brand-mark {
            width: 2rem;
            height: 2rem;
        }

        .panel-auth-brand-name {
            display: none;
        }

        .panel-auth-intro {
            margin-bottom: 0.875rem;
        }

        .panel-auth-title {
            font-size: 1.125rem;
        }

        .panel-auth-subtitle {
            display: none;
        }

        .panel-auth-card-body {
            padding: 1rem 1.25rem 1.25rem;
        }

        .panel-auth-fields {
            gap: 0.5rem;
        }

        .panel-auth-input,
        .panel-auth-submit {
            height: 2.25rem;
        }

        .panel-auth-footer {
            margin-top: 0.875rem;
            padding-top: 0.875rem;
        }
    }

    .panel-link {
        color: rgb(var(--panel-primary));
        text-decoration: none;
        transition: opacity 0.15s;
    }

    .panel-link:hover {
        opacity: 0.85;
        text-decoration: underline;
    }

    .panel-field-error {
        color: rgb(var(--panel-danger));
    }

    .panel-locale-switcher {
        position: relative;
        flex-shrink: 0;
    }

    .panel-locale-menu {
        position: absolute;
        left: 0;
        z-index: 60;
        min-width: 8rem;
        padding: 0.25rem;
        background: rgb(var(--panel-card));
        border: 1px solid rgb(var(--panel-border));
        border-radius: var(--panel-radius);
        box-shadow: 0 8px 24px rgb(0 0 0 / 0.15);
    }

    .panel-locale-switcher--up .panel-locale-menu {
        bottom: calc(100% + 0.5rem);
        top: auto;
    }

    .panel-locale-switcher--down .panel-locale-menu {
        top: calc(100% + 0.5rem);
        bottom: auto;
        left: auto;
        right: 0;
    }

    .panel-topbar-locale-btn {
        flex-shrink: 0;
    }

    .panel-locale-option {
        display: block;
        width: 100%;
        padding: 0.5rem 0.75rem;
        font-size: 0.8125rem;
        text-align: left;
        color: rgb(var(--panel-text));
        background: transparent;
        border: none;
        border-radius: calc(var(--panel-radius) * 0.75);
        cursor: pointer;
    }

    .panel-locale-option:hover,
    .panel-locale-option--active {
        background: rgb(var(--panel-elevated));
        color: rgb(var(--panel-heading));
    }

    .panel-import-field-tag {
        padding: 0.125rem 0.5rem;
        background: rgb(var(--panel-elevated));
        border: 1px solid rgb(var(--panel-border));
        border-radius: 9999px;
    }

    .panel-chart-wrap {
        position: relative;
        min-height: 8rem;
    }

    .panel-chart-wrap--playground {
        min-height: 7.5rem;
        padding: 0.25rem 0;
    }

    .panel-chart-wrap--progression canvas {
        filter: drop-shadow(0 4px 16px rgb(var(--panel-primary) / 0.18));
    }

    .dark .panel-chart-wrap--progression canvas {
        filter: drop-shadow(0 2px 12px rgb(var(--panel-primary) / 0.12));
    }

    .panel-widget-card--chart {
        min-height: 11rem;
    }

    .panel-widget-card--view {
        padding: 0;
        overflow: visible;
    }

    .panel-custom-chart {
        padding: 1.25rem;
    }

    .panel-custom-chart__bar {
        height: 0.5rem;
        border-radius: 9999px;
        background-color: rgb(var(--panel-elevated));
        overflow: hidden;
    }

    .panel-custom-chart__fill {
        height: 100%;
        border-radius: 9999px;
        background: linear-gradient(90deg, rgb(var(--panel-primary)), rgb(var(--panel-accent)));
        transition: width 0.7s ease;
    }

    .panel-auth-verify .panel-auth-actions {
        margin-top: 1.5rem;
    }

    .panel-auth-locale {
        position: fixed;
        top: 1rem;
        right: 4rem;
        z-index: 50;
    }

    /* —— Layout personalizable —— */

    .panel-density-compact .panel-main {
        padding: var(--panel-density-padding) !important;
    }

    .panel-density-compact .panel-card,
    .panel-density-compact .panel-input {
        font-size: 0.875rem;
    }

    .panel-content-boxed .panel-main-content {
        max-width: var(--panel-content-max-width);
        margin-inline: auto;
        width: 100%;
    }

    @media (min-width: 1024px) {
        html.panel-sidebar-collapsed .panel-shell {
            grid-template-columns: var(--panel-sidebar-collapsed-width) minmax(0, 1fr);
        }

        html.panel-sidebar-collapsed .panel-sidebar-brand-text,
        html.panel-sidebar-collapsed .panel-nav-text,
        html.panel-sidebar-collapsed .panel-nav-group-chevron,
        html.panel-sidebar-collapsed .panel-nav-group-children,
        html.panel-sidebar-collapsed .panel-sidebar-footer-links,
        html.panel-sidebar-collapsed .panel-profile-link span:not(.panel-user-avatar),
        html.panel-sidebar-collapsed .panel-sidebar-version,
        html.panel-sidebar-collapsed .panel-impersonation-sidebar-card span,
        html.panel-sidebar-collapsed .panel-impersonation-sidebar-card button span {
            display: none !important;
        }

        html.panel-sidebar-collapsed .panel-nav-group-trigger,
        html.panel-sidebar-collapsed .panel-nav-link,
        html.panel-sidebar-collapsed .panel-nav-static-item {
            justify-content: center;
            padding-inline: 0.5rem;
        }

        html.panel-sidebar-collapsed .panel-sidebar-footer {
            padding-inline: 0.5rem;
        }
    }

    .panel-sidebar-footer-links {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        margin-bottom: 0.75rem;
    }

    .panel-sidebar-footer-link {
        font-size: 0.75rem;
        color: rgb(var(--panel-muted));
        text-decoration: none;
        padding: 0.25rem 0;
    }

    .panel-sidebar-footer-link:hover {
        color: rgb(var(--panel-heading));
    }

    .panel-auth-shell--split {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        max-width: 72rem;
        width: 100%;
    }

    @media (min-width: 1024px) {
        .panel-auth-shell--split {
            grid-template-columns: minmax(0, 28rem) minmax(0, 1fr);
            align-items: stretch;
            min-height: min(90vh, 40rem);
        }

        .panel-auth-body--split .panel-auth-card {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }
    }

    .panel-auth-split-image {
        display: none;
        background-size: cover;
        background-position: center;
        border-radius: 0 var(--panel-radius) var(--panel-radius) 0;
        min-height: 12rem;
    }

    @media (min-width: 1024px) {
        .panel-auth-split-image {
            display: block;
        }
    }

    /* —— Modos de layout —— */

    .panel-chrome-header {
        display: flex;
        flex-shrink: 0;
        align-items: center;
        gap: 1rem;
        height: var(--panel-header-height);
        min-height: var(--panel-header-height);
        padding-inline: 1.25rem;
        border-bottom: 1px solid rgb(var(--panel-border));
    }

    .panel-chrome-header-brand {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        min-width: 0;
    }

    .panel-sidebar-close-btn,
    .panel-sidebar-collapse-btn {
        flex-shrink: 0;
        margin-left: auto;
    }

    .panel-main-column {
        display: flex;
        min-width: 0;
        flex-direction: column;
        min-height: 100dvh;
    }

    .panel-shell--topbar {
        display: flex;
        flex-direction: column;
    }

    .panel-shell--topbar .panel-main-column {
        flex: 1;
        min-width: 0;
    }

    @media (max-width: 1023px) {
        .panel-shell--sidebar,
        .panel-shell--dual,
        .panel-shell--topbar {
            display: flex !important;
            flex-direction: column;
            grid-template-columns: none !important;
        }

        .panel-shell--dual .panel-sidebar,
        .panel-shell--sidebar .panel-sidebar {
            position: fixed;
            height: 100dvh;
            max-height: 100dvh;
        }

        .panel-shell--dual .panel-main-column,
        .panel-shell--sidebar .panel-main-column,
        .panel-shell--topbar .panel-main-column {
            flex: 1 1 auto;
            width: 100%;
            min-width: 0;
            min-height: 0;
        }

        .panel-shell--dual .panel-topbar,
        .panel-shell--topbar .panel-topbar {
            position: sticky;
            top: 0;
            z-index: 35;
        }

        .panel-sidebar-right .panel-sidebar {
            border-left: 1px solid rgb(var(--panel-border));
        }

        html.panel-sidebar-collapsed .panel-sidebar {
            width: var(--panel-sidebar-width);
        }
    }

    @media (min-width: 1024px) {
        .panel-shell--topbar .panel-sidebar--mobile-drawer {
            display: none !important;
        }

        .panel-shell--dual {
            display: grid;
            grid-template-columns: var(--panel-sidebar-width) minmax(0, 1fr);
            min-height: 100dvh;
        }

        .panel-shell--dual .panel-sidebar {
            grid-column: 1;
            height: 100dvh;
            max-height: 100dvh;
        }

        .panel-shell--dual .panel-main-column {
            grid-column: 2;
            min-height: 100dvh;
            min-width: 0;
        }
    }

    .panel-mobile-bar {
        display: none;
    }

    @media (max-width: 1023px) {
        .panel-mobile-bar {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-shrink: 0;
            position: sticky;
            top: 0;
            z-index: 35;
            height: var(--panel-header-height);
            min-height: var(--panel-header-height);
            padding-inline: 1rem;
            border-bottom: 1px solid rgb(var(--panel-border));
            background-color: rgb(var(--panel-surface) / 0.92);
            backdrop-filter: blur(10px);
        }

        .panel-mobile-bar-brand {
            flex: 1;
            min-width: 0;
        }

        .panel-sidebar-footer-links {
            display: none;
        }
    }

    .panel-sidebar-right.panel-shell--sidebar,
    .panel-sidebar-right.panel-shell--dual {
        direction: rtl;
    }

    .panel-sidebar-right .panel-sidebar,
    .panel-sidebar-right .panel-main-column {
        direction: ltr;
    }

    .panel-topbar {
        position: sticky;
        top: 0;
        z-index: 35;
        flex-shrink: 0;
        background-color: rgb(var(--panel-surface) / 0.92);
        backdrop-filter: blur(10px);
    }

    .panel-topbar-inner {
        display: flex;
        align-items: center;
        gap: 1rem;
        height: var(--panel-header-height);
        min-height: var(--panel-header-height);
        padding-inline: 1rem;
    }

    .panel-topbar-inner--sin-marca {
        justify-content: flex-end;
    }

    .panel-topbar--dual {
        border-bottom: 1px solid rgb(var(--panel-border));
    }

    .panel-topbar:not(.panel-topbar--dual) {
        border-bottom: 1px solid rgb(var(--panel-border));
    }

    .panel-topbar-brand {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-shrink: 0;
    }

    .panel-topbar-nav {
        display: none;
        flex: 1;
        min-width: 0;
        align-items: stretch;
    }

    @media (min-width: 1024px) {
        .panel-topbar-nav {
            display: flex;
        }
    }

    .panel-topbar-nav-scroll {
        display: flex;
        flex: 1;
        min-width: 0;
        align-items: center;
        gap: 0.125rem;
    }

    .panel-topbar-dropdown {
        position: relative;
        flex-shrink: 0;
    }

    .panel-topbar-trigger {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.4375rem 0.75rem;
        border: none;
        border-radius: 0.5rem;
        background: transparent;
        font-size: 0.8125rem;
        font-weight: 500;
        color: rgb(var(--panel-muted));
        text-decoration: none;
        white-space: nowrap;
        cursor: pointer;
        transition: background-color 0.15s ease, color 0.15s ease;
    }

    .panel-topbar-trigger:hover,
    .panel-topbar-trigger-active,
    .panel-topbar-trigger-open {
        color: rgb(var(--panel-heading));
        background-color: rgb(var(--panel-elevated));
    }

    .panel-topbar-chevron {
        color: rgb(var(--panel-muted));
        transition: transform 0.15s ease;
    }

    .panel-topbar-dropdown-panel {
        position: absolute;
        top: calc(100% + 0.375rem);
        left: 0;
        z-index: 60;
        min-width: 13rem;
        max-width: min(18rem, 90vw);
        padding: 0.375rem;
        border: 1px solid rgb(var(--panel-border));
        border-radius: 0.625rem;
        background-color: rgb(var(--panel-surface));
        box-shadow: 0 12px 40px rgb(0 0 0 / 0.18);
    }

    .panel-topbar-dropdown-panel.panel-topbar-user-panel {
        right: 0;
        left: auto;
        min-width: 11rem;
    }

    .panel-topbar-sublink {
        display: flex;
        width: 100%;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0.75rem;
        border: none;
        border-radius: 0.375rem;
        background: transparent;
        font-size: 0.8125rem;
        font-weight: 500;
        color: rgb(var(--panel-muted));
        text-align: left;
        text-decoration: none;
        cursor: pointer;
        transition: background-color 0.15s ease, color 0.15s ease;
    }

    .panel-topbar-sublink:hover,
    .panel-topbar-sublink-active {
        color: rgb(var(--panel-heading));
        background-color: rgb(var(--panel-elevated));
    }

    .panel-topbar-sublink-btn {
        font-family: inherit;
    }

    .panel-topbar-user-trigger {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.25rem 0.5rem 0.25rem 0.25rem;
        border: 1px solid rgb(var(--panel-border));
        border-radius: 9999px;
        background: rgb(var(--panel-elevated) / 0.5);
        color: rgb(var(--panel-heading));
        cursor: pointer;
        transition: background-color 0.15s ease;
    }

    .panel-topbar-user-trigger:hover {
        background-color: rgb(var(--panel-elevated));
    }

    .panel-topbar-user-name {
        max-width: 8rem;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 0.8125rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .panel-topbar-version {
        font-size: 0.6875rem;
        font-weight: 500;
    }

    .panel-topbar-actions {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        flex-shrink: 0;
    }

    .panel-topbar-inner:not(.panel-topbar-inner--sin-marca) .panel-topbar-actions {
        margin-left: auto;
    }

    .panel-shell--dual .panel-topbar-inner {
        padding-inline: 1.25rem;
    }

    .panel-table-striped tbody tr:nth-child(even) {
        background-color: rgb(var(--panel-elevated) / 0.45);
    }

    .panel-table-compact th,
    .panel-table-compact td {
        padding-top: 0.375rem;
        padding-bottom: 0.375rem;
        font-size: 0.8125rem;
    }

    /* —— Playground público —— */

    .panel-playground-body {
        height: 100dvh;
        overflow: hidden;
        background: rgb(var(--panel-bg));
        color: rgb(var(--panel-text));
    }

    .panel-playground-root {
        position: relative;
        height: 100dvh;
        overflow: hidden;
    }

    .panel-playground-stage {
        position: relative;
        height: 100dvh;
        width: 100%;
        overflow: hidden;
    }

    .panel-playground-backdrop {
        position: fixed;
        inset: 0;
        z-index: 70;
        border: none;
        padding: 0;
        background: rgb(0 0 0 / 0.5);
        cursor: pointer;
        backdrop-filter: blur(2px);
    }

    .panel-playground-drawer {
        --panel-playground-drawer-width: min(28rem, 100vw);
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        z-index: 80;
        display: flex;
        flex-direction: column;
        width: var(--panel-playground-drawer-width);
        min-height: 0;
        border-right: 1px solid rgb(var(--panel-border));
        background: rgb(var(--panel-surface));
        box-shadow: var(--panel-shadow-lg);
    }

    .panel-playground-drawer--avanzado {
        --panel-playground-drawer-width: min(42rem, 100vw);
    }

    .panel-playground-contenido--avanzado {
        padding: 0;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .panel-playground-drawer-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        flex-shrink: 0;
        padding: 0.875rem 1rem;
        border-bottom: 1px solid rgb(var(--panel-border));
        background: rgb(var(--panel-card));
    }

    .panel-playground-drawer-brand {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        min-width: 0;
        flex: 1;
    }

    .panel-playground-drawer-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        flex-shrink: 0;
        border-radius: 0.5rem;
        color: rgb(var(--panel-primary-fg));
        background: linear-gradient(135deg, rgb(var(--panel-primary)), rgb(var(--panel-accent)));
        box-shadow: 0 4px 12px rgb(var(--panel-primary) / 0.2);
    }

    .panel-playground-drawer-actions {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        flex-shrink: 0;
    }

    .panel-playground-icon-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        border: 1px solid rgb(var(--panel-border));
        border-radius: 0.5rem;
        color: rgb(var(--panel-muted));
        background: rgb(var(--panel-elevated));
        cursor: pointer;
        transition: color 0.15s, border-color 0.15s, background 0.15s;
    }

    .panel-playground-icon-btn:hover {
        color: rgb(var(--panel-heading));
        border-color: rgb(var(--panel-primary) / 0.35);
        background: rgb(var(--panel-primary) / 0.08);
    }

    .panel-playground-grupos {
        display: flex;
        flex-wrap: nowrap;
        gap: 0.375rem;
        flex-shrink: 0;
        overflow-x: auto;
        padding: 0.5rem 0.75rem;
        border-bottom: 1px solid rgb(var(--panel-border));
        background: rgb(var(--panel-bg) / 0.4);
        scrollbar-width: thin;
    }

    .panel-playground-grupo-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        flex-shrink: 0;
        padding: 0.4375rem 0.75rem;
        border: 1px solid transparent;
        border-radius: 9999px;
        font-size: 0.6875rem;
        font-weight: 600;
        color: rgb(var(--panel-muted));
        background: transparent;
        cursor: pointer;
        transition: color 0.15s, background 0.15s, border-color 0.15s;
    }

    .panel-playground-grupo-btn:hover {
        color: rgb(var(--panel-heading));
        background: rgb(var(--panel-elevated));
    }

    .panel-playground-grupo-btn--active {
        color: rgb(var(--panel-heading));
        border-color: rgb(var(--panel-primary) / 0.3);
        background: rgb(var(--panel-primary) / 0.1);
    }

    .panel-playground-contenido {
        flex: 1;
        min-height: 0;
        overflow-y: auto;
        padding: 1rem 0.875rem 1.25rem;
    }

    .panel-playground-inicio-lead {
        font-size: 0.8125rem;
        line-height: 1.55;
        color: rgb(var(--panel-text));
        margin-bottom: 1rem;
    }

    .panel-playground-pasos {
        list-style: none;
        margin: 0 0 1.25rem;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .panel-playground-paso {
        display: flex;
        gap: 0.75rem;
        align-items: flex-start;
    }

    .panel-playground-paso-num {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1.5rem;
        height: 1.5rem;
        flex-shrink: 0;
        border-radius: 9999px;
        font-size: 0.6875rem;
        font-weight: 700;
        color: rgb(var(--panel-primary-fg));
        background: rgb(var(--panel-primary));
    }

    .panel-playground-inicio-acciones {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .panel-playground-exportar-vacio {
        padding: 1.5rem 0.75rem;
        text-align: center;
    }

    .panel-playground-exportar-bloque {
        margin-bottom: 1rem;
        padding: 0.75rem;
        border: 1px solid rgb(var(--panel-border));
        border-radius: 0.625rem;
        background: rgb(var(--panel-card));
    }

    .panel-playground-exportar-cabecera {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        margin-bottom: 0.25rem;
    }

    .panel-playground-codigo {
        margin: 0;
        padding: 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.6875rem;
        line-height: 1.5;
        color: rgb(var(--panel-heading));
        background: rgb(var(--panel-elevated));
        overflow-x: auto;
        white-space: pre;
    }

    .panel-playground-cambios {
        list-style: none;
        margin: 0.5rem 0 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 0.375rem;
    }

    .panel-playground-cambio {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        padding: 0.5rem 0.625rem;
        border: 1px solid rgb(var(--panel-border));
        border-radius: 0.5rem;
        background: rgb(var(--panel-elevated) / 0.5);
    }

    .panel-playground-cambio-info {
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 0.125rem;
    }

    .panel-copiar-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        flex-shrink: 0;
        padding: 0.375rem 0.625rem;
        border: 1px solid rgb(var(--panel-border));
        border-radius: 0.5rem;
        font-size: 0.6875rem;
        font-weight: 600;
        color: rgb(var(--panel-heading));
        background: rgb(var(--panel-elevated));
        cursor: pointer;
        transition: background 0.15s, border-color 0.15s;
    }

    .panel-copiar-btn--sm {
        padding: 0.3125rem;
    }

    .panel-copiar-btn:hover {
        border-color: rgb(var(--panel-primary) / 0.35);
        background: rgb(var(--panel-primary) / 0.08);
    }

    .panel-copiar-btn--ok {
        border-color: rgb(var(--panel-success) / 0.4);
        color: rgb(var(--panel-success));
        background: rgb(var(--panel-success) / 0.1);
    }

    .panel-copiar-icon-ok {
        display: none;
    }

    .panel-copiar-btn--ok .panel-copiar-icon {
        display: none;
    }

    .panel-copiar-btn--ok .panel-copiar-icon-ok {
        display: block;
    }

    .panel-playground-fila {
        padding: 0.75rem;
        border: 1px solid rgb(var(--panel-border));
        border-radius: 0.625rem;
        background: rgb(var(--panel-card));
    }

    .panel-playground-fila--ref {
        background: rgb(var(--panel-elevated) / 0.35);
        opacity: 0.9;
    }

    .panel-playground-fila-cabecera {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        margin-bottom: 0.375rem;
    }

    .panel-playground-fila-label {
        font-size: 0.8125rem;
        font-weight: 600;
        color: rgb(var(--panel-heading));
        line-height: 1.3;
    }

    .panel-playground-fila-ayuda {
        margin: -0.125rem 0 0.5rem;
        font-size: 0.6875rem;
        line-height: 1.4;
        color: rgb(var(--panel-muted));
    }

    .panel-playground-fila-control {
        margin-top: 0.25rem;
    }

    .panel-playground-tecnica-nav {
        display: flex;
        flex-wrap: wrap;
        gap: 0.375rem;
        margin-bottom: 0.75rem;
    }

    .panel-playground-tecnica-link {
        padding: 0.3125rem 0.5rem;
        border: 1px solid rgb(var(--panel-border));
        border-radius: 0.375rem;
        font-size: 0.625rem;
        font-weight: 600;
        color: rgb(var(--panel-muted));
        background: rgb(var(--panel-elevated));
        cursor: pointer;
    }

    .panel-playground-tecnica-link--active {
        color: rgb(var(--panel-heading));
        border-color: rgb(var(--panel-primary) / 0.35);
        background: rgb(var(--panel-primary) / 0.1);
    }

    .panel-playground-avanzado-layout {
        display: grid;
        grid-template-columns: minmax(7.5rem, 9.5rem) minmax(0, 1fr);
        flex: 1;
        min-height: 0;
        height: 100%;
    }

    .panel-playground-avanzado-nav {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        padding: 0.625rem 0.5rem;
        border-right: 1px solid rgb(var(--panel-border));
        background: rgb(var(--panel-bg) / 0.45);
        overflow-y: auto;
    }

    .panel-playground-avanzado-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.375rem;
        width: 100%;
        padding: 0.5rem 0.625rem;
        border: 1px solid transparent;
        border-radius: 0.5rem;
        font-size: 0.6875rem;
        font-weight: 600;
        line-height: 1.25;
        text-align: left;
        color: rgb(var(--panel-muted));
        background: transparent;
        cursor: pointer;
        transition: color 0.15s, background 0.15s, border-color 0.15s;
    }

    .panel-playground-avanzado-link:hover {
        color: rgb(var(--panel-heading));
        background: rgb(var(--panel-elevated));
    }

    .panel-playground-avanzado-link--active {
        color: rgb(var(--panel-heading));
        border-color: rgb(var(--panel-primary) / 0.3);
        background: rgb(var(--panel-primary) / 0.1);
    }

    .panel-playground-avanzado-badge {
        flex-shrink: 0;
        min-width: 1.125rem;
        padding: 0.0625rem 0.375rem;
        border-radius: 9999px;
        font-size: 0.5625rem;
        font-weight: 700;
        text-align: center;
        color: rgb(var(--panel-primary-fg));
        background: rgb(var(--panel-primary));
    }

    .panel-playground-avanzado-panel {
        min-height: 0;
        overflow-y: auto;
        padding: 0.875rem 1rem 1.25rem;
    }

    .panel-playground-avanzado-header {
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid rgb(var(--panel-border));
    }

    .panel-playground-fila--modificada {
        border-color: rgb(var(--panel-primary) / 0.35);
        box-shadow: inset 0 0 0 1px rgb(var(--panel-primary) / 0.08);
    }

    .panel-playground-fila-titulos {
        min-width: 0;
        flex: 1;
    }

    .panel-playground-fila-zona {
        margin: 0.125rem 0 0;
        font-size: 0.625rem;
        font-weight: 500;
        color: rgb(var(--panel-primary));
    }

    .panel-playground-fila-acciones {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        flex-shrink: 0;
    }

    .panel-playground-fila-badge {
        padding: 0.125rem 0.4375rem;
        border-radius: 9999px;
        font-size: 0.5625rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        text-transform: uppercase;
        color: rgb(var(--panel-primary));
        background: rgb(var(--panel-primary) / 0.12);
        border: 1px solid rgb(var(--panel-primary) / 0.25);
    }

    .panel-playground-zonas-leyenda {
        position: absolute;
        top: 0.75rem;
        left: 50%;
        z-index: 55;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        gap: 0.375rem;
        max-width: min(36rem, calc(100% - 2rem));
        padding: 0.375rem 0.625rem;
        border-radius: 9999px;
        border: 1px solid rgb(var(--panel-primary) / 0.25);
        background: rgb(var(--panel-card) / 0.92);
        backdrop-filter: blur(8px);
        transform: translateX(-50%);
        box-shadow: var(--panel-shadow-lg);
        pointer-events: auto;
    }

    .panel-playground-zonas-leyenda-titulo {
        font-size: 0.625rem;
        font-weight: 600;
        color: rgb(var(--panel-muted));
        white-space: nowrap;
    }

    .panel-playground-zonas-chip {
        padding: 0.1875rem 0.5rem;
        border: 1px solid rgb(var(--panel-primary) / 0.3);
        border-radius: 9999px;
        font-size: 0.625rem;
        font-weight: 600;
        color: rgb(var(--panel-heading));
        background: rgb(var(--panel-primary) / 0.1);
        cursor: pointer;
        transition: background 0.15s, transform 0.15s;
    }

    .panel-playground-zonas-chip:hover {
        background: rgb(var(--panel-primary) / 0.18);
        transform: translateY(-1px);
    }

    .panel-playground-zona {
        position: relative;
    }

    .panel-playground-zona--inline {
        display: inline-block;
        vertical-align: middle;
    }

    .panel-playground-zona--menu > .panel-playground-sidebar,
    .panel-playground-zona--menu > aside {
        position: relative;
    }

    .panel-playground-zona--shell {
        display: block;
        height: 100%;
    }

    .panel-playground-zona-badge {
        position: absolute;
        top: 0.375rem;
        right: 0.375rem;
        z-index: 40;
        padding: 0.1875rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.5625rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: rgb(var(--panel-primary-fg));
        background: rgb(var(--panel-primary));
        box-shadow: 0 2px 8px rgb(var(--panel-primary) / 0.35);
        pointer-events: none;
        animation: panel-playground-badge-in 0.25s ease;
    }

    .panel-playground-zona--modificada::before,
    .panel-playground-zona--activa::before,
    .panel-playground-zona--flash::before {
        content: '';
        position: absolute;
        inset: 0;
        z-index: 30;
        border-radius: inherit;
        pointer-events: none;
    }

    .panel-playground-zona--modificada::before {
        border: 2px dashed rgb(var(--panel-primary) / 0.45);
        background: rgb(var(--panel-primary) / 0.04);
    }

    .panel-playground-zona--activa::before,
    .panel-playground-zona--flash::before {
        border: 2px solid rgb(var(--panel-primary));
        background: rgb(var(--panel-primary) / 0.08);
        animation: panel-playground-zona-pulse 1.2s ease infinite;
    }

    @keyframes panel-playground-badge-in {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes panel-playground-zona-pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.55; }
    }

    .panel-playground-section-intro {
        margin-bottom: 1rem;
        padding-bottom: 0.875rem;
        border-bottom: 1px solid rgb(var(--panel-border));
    }

    .panel-playground-group-label {
        margin-bottom: 0.5rem;
        font-size: 0.625rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: rgb(var(--panel-primary));
    }

    .panel-playground-option-group {
        margin-bottom: 1rem;
    }

    .panel-playground-option-list {
        display: flex;
        flex-direction: column;
        gap: 0.625rem;
    }

    .panel-playground-ref-block {
        margin-top: 0.5rem;
        border: 1px solid rgb(var(--panel-border));
        border-radius: 0.625rem;
        background: rgb(var(--panel-bg) / 0.35);
        overflow: hidden;
    }

    .panel-playground-ref-summary {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        padding: 0.625rem 0.875rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: rgb(var(--panel-muted));
        cursor: pointer;
        list-style: none;
        user-select: none;
    }

    .panel-playground-ref-summary::-webkit-details-marker {
        display: none;
    }

    .panel-playground-ref-summary::before {
        content: '▸';
        margin-right: 0.375rem;
        font-size: 0.625rem;
        transition: transform 0.15s;
    }

    .panel-playground-ref-block[open] .panel-playground-ref-summary::before {
        transform: rotate(90deg);
    }

    .panel-playground-ref-count {
        padding: 0.125rem 0.375rem;
        border-radius: 9999px;
        font-size: 0.625rem;
        font-weight: 700;
        color: rgb(var(--panel-muted));
        background: rgb(var(--panel-elevated));
    }

    .panel-playground-ref-block .panel-playground-option-list {
        padding: 0 0.75rem 0.75rem;
    }

    .panel-playground-config-key {
        font-size: 0.625rem;
        color: rgb(var(--panel-muted));
        word-break: break-all;
    }

    .panel-playground-code {
        display: block;
        padding: 0.5rem 0.625rem;
        border-radius: 0.375rem;
        font-size: 0.6875rem;
        line-height: 1.45;
        color: rgb(var(--panel-heading));
        background: rgb(var(--panel-elevated));
        word-break: break-word;
    }

    .panel-playground-type-tag {
        display: inline-block;
        margin-top: 0.375rem;
        padding: 0.125rem 0.375rem;
        border-radius: 0.25rem;
        font-size: 0.625rem;
        color: rgb(var(--panel-muted));
        background: rgb(var(--panel-elevated));
    }

    .panel-playground-color-input {
        width: 100%;
        height: 2.25rem;
        padding: 0.125rem;
        border: 1px solid rgb(var(--panel-border));
        border-radius: 0.5rem;
        background: rgb(var(--panel-elevated));
        cursor: pointer;
    }

    .panel-playground-switch {
        display: inline-flex;
        align-items: center;
        gap: 0.625rem;
        cursor: pointer;
    }

    .panel-playground-switch-input {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }

    .panel-playground-switch-track {
        position: relative;
        width: 2.25rem;
        height: 1.25rem;
        flex-shrink: 0;
        border-radius: 9999px;
        background: rgb(var(--panel-border));
        transition: background 0.2s;
    }

    .panel-playground-switch-track::after {
        content: '';
        position: absolute;
        top: 0.125rem;
        left: 0.125rem;
        width: 1rem;
        height: 1rem;
        border-radius: 9999px;
        background: rgb(var(--panel-card));
        box-shadow: 0 1px 2px rgb(0 0 0 / 0.15);
        transition: transform 0.2s;
    }

    .panel-playground-switch-input:checked + .panel-playground-switch-track {
        background: rgb(var(--panel-primary));
    }

    .panel-playground-switch-input:checked + .panel-playground-switch-track::after {
        transform: translateX(1rem);
    }

    .panel-playground-switch-text {
        font-size: 0.6875rem;
    }

    .panel-playground-fab {
        position: fixed;
        bottom: 1rem;
        left: 1rem;
        z-index: 60;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        max-width: calc(100vw - 2rem);
        padding: 0.625rem 1rem;
        border: 1px solid rgb(var(--panel-border));
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        color: rgb(var(--panel-heading));
        background: rgb(var(--panel-card) / 0.95);
        cursor: pointer;
        backdrop-filter: blur(10px);
        box-shadow: var(--panel-shadow-lg);
        transition: transform 0.15s, box-shadow 0.15s;
    }

    .panel-playground-fab:hover {
        transform: translateY(-1px);
        box-shadow: 0 12px 28px rgb(0 0 0 / 0.12);
    }

    .panel-playground-fab-text {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    @media (max-width: 639px) {
        .panel-playground-drawer {
            --panel-playground-drawer-width: 100vw;
        }

        .panel-playground-fab-text {
            display: none;
        }

        .panel-playground-fab {
            width: 2.75rem;
            height: 2.75rem;
            padding: 0;
            justify-content: center;
            border-radius: 9999px;
        }
    }

    .panel-playground-escenario {
        height: 100%;
        min-height: 0;
        overflow: hidden;
        isolation: isolate;
    }

    .panel-playground-escenario.panel-body {
        min-height: 0;
    }

    .panel-playground-escenario .panel-sidebar {
        position: relative !important;
        top: auto !important;
        bottom: auto !important;
        left: auto !important;
        right: auto !important;
        z-index: auto !important;
        width: var(--panel-sidebar-width) !important;
        height: 100% !important;
        max-height: 100% !important;
        transform: none !important;
        flex-shrink: 0;
    }

    .panel-playground-shell {
        height: 100%;
        min-height: 0 !important;
    }

    .panel-playground-escenario.panel-layout-sidebar .panel-playground-shell,
    .panel-playground-escenario.panel-layout-dual .panel-playground-shell {
        display: grid;
        grid-template-columns: var(--panel-sidebar-width) minmax(0, 1fr);
        grid-template-rows: minmax(0, 1fr);
        height: 100%;
        min-height: 0;
    }

    .panel-playground-escenario.panel-layout-topbar .panel-playground-shell {
        display: flex;
        flex-direction: column;
        height: 100%;
        min-height: 0;
    }

    .panel-playground-escenario .panel-main-column {
        display: flex;
        flex-direction: column;
        min-width: 0;
        min-height: 0;
        flex: 1;
    }

    .panel-playground-escenario .panel-main {
        position: relative;
        flex: 1;
        min-height: 0;
        overflow: auto;
    }

    .panel-playground-main {
        height: auto;
    }

    .panel-playground-escenario.panel-sidebar-collapsed.panel-layout-sidebar .panel-playground-shell,
    .panel-playground-escenario.panel-sidebar-collapsed.panel-layout-dual .panel-playground-shell {
        grid-template-columns: var(--panel-sidebar-collapsed-width) minmax(0, 1fr);
    }

    @media (min-width: 1024px) {
        .panel-playground-escenario.panel-layout-topbar .panel-playground-sidebar {
            display: none !important;
        }

        .panel-playground-escenario.panel-sidebar-collapsed .panel-sidebar-brand-text,
        .panel-playground-escenario.panel-sidebar-collapsed .panel-nav-text,
        .panel-playground-escenario.panel-sidebar-collapsed .panel-nav-group-chevron,
        .panel-playground-escenario.panel-sidebar-collapsed .panel-nav-group-children,
        .panel-playground-escenario.panel-sidebar-collapsed .panel-sidebar-footer-links,
        .panel-playground-escenario.panel-sidebar-collapsed .panel-profile-link span:not(.panel-user-avatar),
        .panel-playground-escenario.panel-sidebar-collapsed .panel-sidebar-version,
        .panel-playground-escenario.panel-sidebar-collapsed .panel-impersonation-sidebar-card span,
        .panel-playground-escenario.panel-sidebar-collapsed .panel-impersonation-sidebar-card button span {
            display: none;
        }

        .panel-playground-escenario.panel-sidebar-collapsed .panel-nav-group-trigger,
        .panel-playground-escenario.panel-sidebar-collapsed .panel-nav-link,
        .panel-playground-escenario.panel-sidebar-collapsed .panel-nav-static-item {
            justify-content: center;
            padding-inline: 0.5rem;
        }

        .panel-playground-escenario.panel-sidebar-collapsed .panel-sidebar-footer {
            padding-inline: 0.5rem;
        }
    }

    .panel-playground-overlay {
        position: absolute;
        inset: 0;
        z-index: 40;
    }

    @media (max-width: 1023px) {
        .panel-playground-escenario .panel-playground-shell {
            display: flex;
            flex-direction: column;
            height: 100%;
            grid-template-columns: none !important;
        }

        .panel-playground-escenario.panel-layout-dual .panel-playground-shell,
        .panel-playground-escenario.panel-layout-sidebar .panel-playground-shell {
            display: flex !important;
            flex-direction: column;
        }

        .panel-playground-escenario .panel-playground-sidebar {
            position: fixed !important;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 50 !important;
            width: var(--panel-sidebar-width);
            height: 100dvh;
            max-height: 100dvh;
            transform: translateX(-100%);
            transition: transform 0.2s ease;
        }

        .panel-playground-escenario .panel-playground-sidebar--right {
            left: auto;
            right: 0;
            transform: translateX(100%);
        }

        .panel-playground-escenario .panel-playground-sidebar--open {
            transform: translateX(0) !important;
        }

        .panel-playground-escenario .panel-main-column {
            flex: 1 1 auto;
            width: 100%;
            min-width: 0;
        }
    }

    .panel-playground-fake-badge {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        z-index: 10;
        padding: 0.25rem 0.625rem;
        border-radius: 9999px;
        font-size: 0.625rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: rgb(var(--panel-warning));
        background: rgb(var(--panel-warning) / 0.12);
        border: 1px solid rgb(var(--panel-warning) / 0.25);
    }

    .panel-playground-escenario.panel-content-boxed .panel-main-content,
    .panel-playground-escenario.panel-content-boxed .panel-playground-preview {
        max-width: var(--panel-content-max-width);
        margin-inline: auto;
        width: 100%;
    }

    .panel-playground-charts {
        margin-top: 2rem;
        padding-top: 1.75rem;
        border-top: 1px solid rgb(var(--panel-border) / 0.65);
    }

    .panel-playground-charts-grid {
        display: grid;
        gap: 1.25rem;
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }

    @media (min-width: 768px) {
        .panel-playground-charts-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (min-width: 1280px) {
        .panel-playground-charts-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    .panel-playground-chart-card {
        position: relative;
        padding: 1rem 1.125rem 1.125rem;
        min-height: 12.5rem;
        transition: border-color 0.2s, box-shadow 0.2s, transform 0.2s;
    }

    .panel-playground-chart-card--active {
        grid-column: 1 / -1;
        padding: 1.25rem 1.375rem 1.375rem;
        min-height: 14rem;
        border-color: rgb(var(--panel-primary) / 0.45);
        box-shadow: 0 0 0 1px rgb(var(--panel-primary) / 0.15), 0 12px 32px rgb(var(--panel-primary) / 0.1);
    }

    @media (min-width: 768px) {
        .panel-playground-chart-card--active {
            grid-column: span 2;
        }
    }

    .panel-playground-chart-card--active .panel-chart-wrap--playground {
        min-height: 10rem;
    }

    .panel-playground-fila--toggles {
        display: flex;
        flex-direction: column;
        gap: 0.625rem;
    }

    .panel-playground-switch-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        cursor: pointer;
    }

    .panel-playground-chart-activo {
        padding: 0.125rem 0.4375rem;
        border-radius: 9999px;
        font-size: 0.5625rem;
        font-weight: 700;
        text-transform: uppercase;
        color: rgb(var(--panel-primary-fg));
        background: rgb(var(--panel-primary));
    }

    .panel-playground-chart-tipos {
        display: flex;
        flex-wrap: wrap;
        gap: 0.375rem;
    }

    .panel-playground-chart-tipo {
        padding: 0.375rem 0.625rem;
        border: 1px solid rgb(var(--panel-border));
        border-radius: 9999px;
        font-size: 0.6875rem;
        font-weight: 600;
        color: rgb(var(--panel-muted));
        background: rgb(var(--panel-elevated));
        cursor: pointer;
        transition: color 0.15s, border-color 0.15s, background 0.15s;
    }

    .panel-playground-chart-tipo:hover {
        color: rgb(var(--panel-heading));
        border-color: rgb(var(--panel-primary) / 0.3);
    }

    .panel-playground-chart-tipo--active {
        color: rgb(var(--panel-heading));
        border-color: rgb(var(--panel-primary) / 0.4);
        background: rgb(var(--panel-primary) / 0.12);
    }

    .panel-playground-range {
        accent-color: rgb(var(--panel-primary));
    }
</style>
