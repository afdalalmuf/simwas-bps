<div style="gap:5px" class="d-flex flex-column dashboard-card my-4">
    <div class="d-flex flex-row dashboard-card flex-wrap" style="gap:15px">

        
        <div class="card p-4 mb-2 col-lg">
            <div class="d-flex justify-content-between">
                <div class="d-flex flex-row align-items-center">
                    <div class="icon bg-info"><i class="fa-solid fa-bullseye fas text-white"></i> </div>
                    <div class="ms-2 c-details mx-3">
                        <h6 class="mb-0 text-dark">Jumlah Objek</h6>
                    </div>
                </div>
                <a href="/admin/master-unit-kerja" class="arrow-button-card" type="button" class="rounded-circle"><i
                        class="fa-solid fa-arrow-right"></i></a>
            </div>
            <div class="d-flex flex-row justify-content-around align-items-center" style="gap:5px">
                <div class="d-flex mt-3 align-items-center flex-column">
                    <h1 class="text-dark mx-3" style="font-size: 3em; font-weight: bold;">
                        {{ $unitKerjaCount }}</h1>
                    <span class="text-dark text-center">Unit Kerja</span>
                </div>
                <div style="border-left: 1.5px solid rgba(92, 92, 92, 0.4); height: 70%;opacity: 0.5;"></div>
                <div class="d-flex mt-3 align-items-center flex-column">
                    <h1 class="text-dark mx-3" style="font-size: 3em; font-weight: bold;">
                        {{ $satuanKerjaCount }}</h1>
                    <span class="text-dark text-center">Satuan Kerja</span>
                </div>
                <div style="border-left: 1.5px solid rgba(92, 92, 92, 0.4); height: 70%;opacity: 0.5;"></div>
                <div class="d-flex mt-3 align-items-center flex-column">
                    <h1 class="text-dark mx-3" style="font-size: 3em; font-weight: bold;">
                        {{ $wilayahKerjaCount }}</h1>
                    <span class="text-dark text-center">Wilayah</span>
                </div>
            </div>
        </div>

        <div class="card p-4 mb-2 col-lg">
            <div class="d-flex justify-content-between">
                <div class="d-flex flex-row align-items-center">
                    <div class="icon bg-primary"><i class="fas fa-solid fa-bullseye text-white"></i></div>
                    <div class="ms-2 c-details mx-3">
                        <h6 class="mb-0 text-dark">Jumlah Pengawasan Pada Unit Kerja/Satuan Kerja</h6>
                    </div>
                </div>
                <a href="{{route('auditee.schedule', 'year='.$year)}}" class="arrow-button-card" type="button" class="rounded-circle"><i
                        class="fa-solid fa-arrow-right"></i></a>
            </div>
            <div class="d-flex flex-row justify-content-around align-items-center" style="gap:5px">
                
                <div class="d-flex mt-3 align-items-center flex-column">
                    <h1 class="text-dark mx-3" style="font-size: 3em; font-weight: bold;">
                        {{ $auditeeCount }}</h1>
                    <span class="text-dark text-center">Pengawasan </span>
                </div>
            </div>
        </div>


    </div>

</div>
