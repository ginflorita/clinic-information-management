import './bootstrap';

import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

import $ from 'jquery';
window.$ = window.jQuery = $;

import { DataTable } from 'datatables.net-bs5';
window.DataTable = DataTable;

// select2's UMD wrapper exports a factory that must be invoked explicitly
// under Vite's CommonJS interop — a bare side-effect import is a no-op here.
import select2Init from 'select2';
select2Init(window, $);
