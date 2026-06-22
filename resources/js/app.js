import './bootstrap';

import Alpine from 'alpinejs';
import { posOffline } from './pos-offline.js'

Alpine.data('posOffline', posOffline)

window.Alpine = Alpine
Alpine.start()