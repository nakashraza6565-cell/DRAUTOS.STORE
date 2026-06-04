<style>
@font-face {
    font-family: 'Revue';
    src: url("{{ asset('revue/reve.ttf') }}") format("truetype");
}
.watermark-shared {
    position: fixed; 
    top: 35%; 
    left: 50%; 
    transform: translate(-50%, -50%);
    font-size: 300px; 
    color: #000; 
    opacity: 0.04; 
    z-index: 0;
    font-family: 'Revue', sans-serif; 
    pointer-events: none;
    white-space: nowrap;
}
.watermark-thermal {
    position: absolute; 
    top: 40%; 
    left: 50%; 
    transform: translate(-50%, -50%);
    font-size: 140px; 
    color: #000; 
    opacity: 0.05; 
    z-index: 0; 
    pointer-events: none;
    font-family: 'Revue', sans-serif;
    white-space: nowrap;
}
</style>

@if(isset($type) && $type === 'thermal')
    <div class="watermark-thermal">DR</div>
@else
    <div class="watermark-shared">DR</div>
@endif
