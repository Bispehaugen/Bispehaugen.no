<div class="intern bunn">
    <p>
        Hei <?php echo brukerlenke(innlogget_bruker(), Navnlengde::Fornavn); ?>!
    </p>
    
    <ul class="mobil brukervalg">
        <?php inkluder_side_fra_undermappe("intern/bruker_valg"); ?>
    </ul>
</div>