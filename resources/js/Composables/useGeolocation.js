import { ref, computed } from 'vue';

export function useGeolocation() {
    const coordinates = ref(null);
    const error = ref(null);
    const loading = ref(false);
    
    const isSupported = computed(() => 'geolocation' in navigator);
    
    const getCurrentPosition = () => {
        return new Promise((resolve, reject) => {
            if (!isSupported.value) {
                const err = new Error('Geolocation is not supported by this browser.');
                error.value = err;
                reject(err);
                return;
            }
            
            loading.value = true;
            error.value = null;
            
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    coordinates.value = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude,
                        accuracy: position.coords.accuracy,
                        timestamp: position.timestamp
                    };
                    loading.value = false;
                    resolve(coordinates.value);
                },
                (err) => {
                    let errorMessage = 'Unable to retrieve location.';
                    
                    switch (err.code) {
                        case err.PERMISSION_DENIED:
                            errorMessage = 'Location permission denied. Please enable location access in your browser settings.';
                            break;
                        case err.POSITION_UNAVAILABLE:
                            errorMessage = 'Location information is unavailable.';
                            break;
                        case err.TIMEOUT:
                            errorMessage = 'Location request timed out.';
                            break;
                    }
                    
                    error.value = { code: err.code, message: errorMessage };
                    loading.value = false;
                    reject(error.value);
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        });
    };
    
    const watchPosition = (callback) => {
        if (!isSupported.value) {
            return null;
        }
        
        return navigator.geolocation.watchPosition(
            (position) => {
                coordinates.value = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    accuracy: position.coords.accuracy,
                    timestamp: position.timestamp
                };
                callback(coordinates.value);
            },
            (err) => {
                error.value = err;
                callback(null, err);
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    };
    
    return {
        coordinates,
        error,
        loading,
        isSupported,
        getCurrentPosition,
        watchPosition
    };
}
