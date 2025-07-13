/**
 * jVectorMap Fix - Mencegah error NaN pada jVectorMap
 * 
 * File ini mengatasi masalah error jVectorMap yang muncul ketika:
 * 1. Tidak ada elemen peta di halaman
 * 2. Data peta tidak valid (NaN)
 */
$(document).ready(function() {
    // Tunggu semua elemen DOM dimuat
    setTimeout(function() {
        // Cek apakah ada elemen jVectorMap di halaman
        if ($('.jvectormap-container').length === 0) {
            console.log('Tidak ada elemen jVectorMap di halaman ini');
            
            // Jika jvm sudah dimuat, override fungsi yang menyebabkan error
            if (typeof window.jvm !== 'undefined') {
                // Fix untuk error: "Error: <g> attribute transform: Expected number, "scale(NaN) translate..."
                if (typeof jvm.SVGCanvasElement !== 'undefined') {
                    const originalApplyTransformParams = jvm.SVGCanvasElement.prototype.applyTransformParams;
                    jvm.SVGCanvasElement.prototype.applyTransformParams = function(scale, transX, transY) {
                        if (isNaN(scale) || isNaN(transX) || isNaN(transY)) {
                            console.warn('jVectorMap: Parameter tidak valid', { scale, transX, transY });
                            return;
                        }
                        originalApplyTransformParams.call(this, scale, transX, transY);
                    };
                }
                
                // Fix untuk error: "Error: <circle> attribute cx: Expected length, "NaN"
                if (typeof jvm.AbstractElement !== 'undefined') {
                    const originalApplyAttr = jvm.AbstractElement.prototype.applyAttr;
                    jvm.AbstractElement.prototype.applyAttr = function(property, value) {
                        if (value === 'NaN' || value === NaN || (typeof value === 'string' && value.includes('NaN'))) {
                            console.warn('jVectorMap: Nilai atribut tidak valid', { property, value });
                            return;
                        }
                        originalApplyAttr.call(this, property, value);
                    };
                }
            }
        } else {
            console.log('Elemen jVectorMap ditemukan, inisialisasi normal');
        }
    }, 500); // Tunggu 500ms untuk memastikan semua elemen dimuat
}); 