<style>
    .reservations-page {
        display: flex;
        flex-direction: column;
        gap: 24px;
        color: var(--text-main);
    }

    .reservation-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .reservation-tabs {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px;
        border-radius: 8px;
        background: #f2e9d9;
    }

    .reservation-tab {
        min-width: 92px;
        padding: 10px 16px;
        border-radius: 6px;
        border: 0;
        color: var(--text-sub);
        font: 500 13px/1.2 inherit;
        text-align: center;
        text-decoration: none;
        transition: background 0.18s ease, color 0.18s ease;
    }

    .reservation-tab.active,
    .reservation-tab:hover {
        background: var(--sidebar);
        color: #fff;
    }

    .reservation-date-btn {
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 42px;
        padding: 0 18px;
        border: 0;
        border-radius: 6px;
        background: #6f5d43;
        color: #fff;
        cursor: pointer;
        font: 600 13px/1 inherit;
    }

    .reservation-date-form {
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .reservation-date-input {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .reservation-date-reset {
        min-height: 42px;
        display: inline-flex;
        align-items: center;
        padding: 0 14px;
        border: 1px solid rgba(53, 64, 36, 0.14);
        border-radius: 6px;
        background: #fff;
        color: var(--sidebar);
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
    }

    .reservation-card {
        overflow: hidden;
        border: 1px solid rgba(53, 64, 36, 0.12);
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.86);
        box-shadow: 0 10px 30px rgba(33, 27, 15, 0.05);
    }

    .reservation-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 20px 24px;
    }

    .reservation-card-title {
        color: var(--sidebar);
        font-size: 20px;
        font-weight: 600;
    }

    .reservation-count {
        flex: 0 0 auto;
        padding: 4px 10px;
        border-radius: 999px;
        background: #f2ddb9;
        color: #6f5d43;
        font-size: 11px;
        font-weight: 700;
    }

    .reservation-table-wrap {
        overflow-x: auto;
    }

    .reservation-table {
        min-width: 860px;
        width: 100%;
        border-collapse: collapse;
    }

    .reservation-table thead {
        background: #eee4d3;
    }

    .reservation-table th {
        padding: 15px 24px;
        color: var(--text-sub);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }

    .reservation-table td {
        padding: 18px 24px;
        border-top: 1px solid rgba(53, 64, 36, 0.08);
        vertical-align: middle;
        color: var(--text-sub);
        font-size: 13px;
    }

    .reservation-customer {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 210px;
    }

    .reservation-avatar {
        width: 34px;
        height: 34px;
        flex: 0 0 34px;
        border-radius: 999px;
        background: #dfe7b4;
        display: grid;
        place-items: center;
        color: var(--sidebar);
        font-size: 12px;
        font-weight: 700;
    }

    .reservation-primary {
        margin: 0;
        color: var(--sidebar);
        font-size: 13px;
        font-weight: 600;
    }

    .reservation-muted {
        margin: 2px 0 0;
        color: var(--text-sub);
        font-size: 11px;
    }

    .reservation-capacity {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .reservation-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 9px;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .reservation-status::before {
        content: "";
        width: 5px;
        height: 5px;
        border-radius: 999px;
        background: currentColor;
    }

    .status-pending {
        background: #fff0b8;
        color: #986d00;
    }

    .status-confirmed {
        background: #caefd4;
        color: #1d8845;
    }

    .status-completed {
        background: #eadcc9;
        color: #6c6255;
    }

    .status-cancelled {
        background: #f6d4d4;
        color: #9f2f2f;
    }

    .reservation-detail-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        min-height: 32px;
        padding: 0 12px;
        border: 1px solid rgba(53, 64, 36, 0.16);
        border-radius: 6px;
        background: #fff;
        color: var(--sidebar);
        cursor: pointer;
        font: 700 12px/1 inherit;
        white-space: nowrap;
        transition: background 0.18s ease, color 0.18s ease, border-color 0.18s ease;
    }

    .reservation-detail-btn:hover {
        border-color: var(--sidebar);
        background: var(--sidebar);
        color: #fff;
    }

    .reservation-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 24px;
        border-top: 1px solid rgba(53, 64, 36, 0.08);
    }

    .reservation-footnote {
        color: var(--text-sub);
        font-size: 11px;
    }

    .reservation-pages {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .reservation-page-btn {
        width: 34px;
        height: 34px;
        display: inline-grid;
        place-items: center;
        border: 1px solid rgba(53, 64, 36, 0.1);
        border-radius: 6px;
        background: #fff;
        color: var(--text-sub);
        text-decoration: none;
        font-size: 13px;
    }

    .reservation-page-btn.active {
        background: var(--sidebar);
        color: #fff;
        font-weight: 700;
    }

    .reservation-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
    }

    .reservation-stat {
        min-height: 112px;
        position: relative;
        overflow: hidden;
        border-radius: 8px;
        padding: 20px;
        border: 1px solid rgba(53, 64, 36, 0.08);
    }

    .reservation-stat::after {
        position: absolute;
        right: -10px;
        bottom: -20px;
        color: rgba(53, 64, 36, 0.12);
        font-family: "Font Awesome 6 Free";
        font-size: 66px;
        font-weight: 900;
        line-height: 1;
    }

    .reservation-stat.occupancy {
        background: #faecd8;
    }

    .reservation-stat.occupancy::after {
        content: "\f201";
    }

    .reservation-stat.new {
        background: var(--sidebar);
    }

    .reservation-stat.new::after {
        content: "\f058";
        color: rgba(218, 232, 192, 0.12);
    }

    .reservation-stat.available {
        background: #efe0cd;
    }

    .reservation-stat.available::after {
        content: "\f00c";
    }

    .reservation-stat-label {
        margin: 0 0 6px;
        color: var(--text-sub);
        font-size: 12px;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .reservation-stat.new .reservation-stat-label,
    .reservation-stat.new .reservation-stat-value {
        color: var(--accent-pill);
    }

    .reservation-stat-value {
        margin: 0;
        color: var(--sidebar);
        font-size: 34px;
        font-weight: 800;
        line-height: 1;
    }

    .reservation-stat-value span {
        font-size: 14px;
        font-weight: 500;
    }

    .reservation-stat-note {
        margin: 10px 0 0;
        color: rgba(33, 27, 15, 0.66);
        font-size: 12px;
    }

    .reservation-empty {
        padding: 26px 24px;
        color: var(--text-sub);
        text-align: center;
    }

    .reservation-modal {
        position: fixed;
        inset: 0;
        z-index: 100;
        display: none;
        place-items: center;
        padding: 24px;
    }

    .reservation-modal.open {
        display: grid;
    }

    .reservation-modal-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(33, 27, 15, 0.46);
    }

    .reservation-modal-panel {
        position: relative;
        z-index: 1;
        width: min(460px, 100%);
        max-height: min(620px, calc(100vh - 48px));
        overflow-y: auto;
        border-radius: 8px;
        background: #fffaf2;
        box-shadow: 0 24px 70px rgba(33, 27, 15, 0.24);
    }

    .reservation-modal-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 22px 24px;
        border-bottom: 1px solid rgba(53, 64, 36, 0.1);
    }

    .reservation-modal-header h3 {
        margin: 2px 0 4px;
        color: var(--sidebar);
        font-size: 20px;
        line-height: 1.2;
    }

    .reservation-modal-header p {
        margin: 0;
        color: var(--text-sub);
        font-size: 12px;
    }

    .reservation-modal-kicker {
        color: #6f5d43;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }

    .reservation-modal-close {
        width: 34px;
        height: 34px;
        flex: 0 0 34px;
        display: inline-grid;
        place-items: center;
        border: 1px solid rgba(53, 64, 36, 0.12);
        border-radius: 6px;
        background: #fff;
        color: var(--text-sub);
        cursor: pointer;
    }

    .reservation-order-list {
        padding: 10px 24px 24px;
    }

    .reservation-order-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 14px 0;
        border-bottom: 1px solid rgba(53, 64, 36, 0.08);
    }

    .reservation-order-item:last-child {
        border-bottom: 0;
    }

    .reservation-order-item span {
        min-width: 44px;
        padding: 5px 10px;
        border-radius: 999px;
        background: #f2e9d9;
        color: var(--sidebar);
        font-size: 12px;
        font-weight: 800;
        text-align: center;
    }

    .reservation-order-items {
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px solid rgba(53, 64, 36, 0.08);
    }

    .reservation-order-items .reservation-order-item span {
        background: transparent;
        color: var(--text-main);
        font-size: 13px;
        white-space: nowrap;
    }

    .reservation-order-items .reservation-order-item:last-child {
        border-bottom: 0;
    }

    .reservation-actions {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .reservation-delete-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 7px 12px;
        border: 1px solid rgba(186, 26, 26, 0.25);
        border-radius: 6px;
        background: transparent;
        color: var(--accent-danger, #ba1a1a);
        font: 600 12px/1.2 inherit;
        cursor: pointer;
        transition: background 0.18s ease, color 0.18s ease;
    }

    .reservation-delete-btn:hover {
        background: rgba(186, 26, 26, 0.08);
    }

    .reservation-delete-form {
        display: inline-flex;
    }

    .reservation-modal-footer {
        padding: 0 24px 20px;
    }

    .reservation-modal-delete {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 18px;
        border: 1px solid rgba(186, 26, 26, 0.3);
        border-radius: 6px;
        background: transparent;
        color: var(--accent-danger, #ba1a1a);
        font: 600 13px/1.2 inherit;
        cursor: pointer;
        transition: background 0.18s ease;
    }

    .reservation-modal-delete:hover {
        background: rgba(186, 26, 26, 0.08);
    }

    @media (max-width: 860px) {
        .reservation-toolbar,
        .reservation-card-footer {
            align-items: stretch;
            flex-direction: column;
        }

        .reservation-tabs,
        .reservation-date-form,
        .reservation-date-btn,
        .reservation-pages {
            width: 100%;
        }

        .reservation-tab {
            flex: 1;
            min-width: 0;
            padding-left: 10px;
            padding-right: 10px;
        }

        .reservation-date-btn {
            justify-content: center;
        }

        .reservation-date-reset {
            justify-content: center;
            flex: 1;
        }

        .reservation-pages {
            justify-content: flex-end;
        }

        .reservation-stats {
            grid-template-columns: 1fr;
        }
    }

    .btn-lunasi {
        background-color: #4caf50 !important;
        color: white !important;
        border-color: #4caf50 !important;
    }
    .btn-lunasi:hover {
        background-color: #388e3c !important;
        border-color: #388e3c !important;
        color: white !important;
    }
    .btn-selesaikan {
        background-color: #2196f3 !important;
        color: white !important;
        border-color: #2196f3 !important;
    }
    .btn-selesaikan:hover {
        background-color: #1976d2 !important;
        border-color: #1976d2 !important;
        color: white !important;
    }
</style>
