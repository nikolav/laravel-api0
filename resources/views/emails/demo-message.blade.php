  <x-email-layout-default>
      <x-slot:title>
          Hello, this is test message.
      </x-slot:title>
      <x-slot:header>
          <em>Lorem ipsum dolor, sit amet consectetur adipisicing elit.</em>
      </x-slot:header>

      <section>
          <div>
              @if ($msg)
                  <p>
                      {{ $msg }}
                  </p>
              @endif
              <p>
                  Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quae sunt vitae voluptates rerum maxime aut
                  assumenda neque sit, minima tenetur debitis voluptas.
              </p>
          </div>

          <x-email-img src="https://nikolav.rs/lotr.webp" alt="LOTR fellowship of the ring💪" max-width="320" />

          <hr style="width: 100%;">
          <div style="display: flex; justify-content: center; gap: 1em;">
              <x-email-button url="https://nikolav.rs" label="nikolav.rs" />
              <x-email-button url="https://google.rs" label="google" />
          </div>


      </section>

      <x-slot:footer>
          <p>
              Lorem ipsum dolor sit amet consectetur adipisicing elit.
          </p>
      </x-slot:footer>

  </x-email-layout-default>
