  <x-email-layout-default title="Hello, this is test message.">
      <x-slot:header>
          <strong>Simple test message.</strong>
      </x-slot:header>
      <x-slot:sub-header>
          <em>Lorem ipsum dolor, sit amet consectetur adipisicing elit.</em>
      </x-slot:sub-header>


      <main>
          <x-email-img src="https://nikolav.rs/lotr.webp" alt="LOTR fellowship of the ring💪" max-width="320" />

          <div>
              @isset($data['message'])
                  <p>
                      {{ $data['message'] }}
                  </p>
              @endisset
              <p>
                  Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quae sunt vitae voluptates rerum maxime aut
                  assumenda neque sit, minima tenetur debitis voluptas.
              </p>
          </div>

          <div style="display: flex; justify-content: center; gap: 1em;">
              <x-email-button url="https://nikolav.rs" label="nikolav.rs" />
              <x-email-button url="https://google.rs" label="google" />
          </div>


      </main>

      <x-slot:footer>
          <p>
              Lorem ipsum dolor sit amet consectetur adipisicing elit.
          </p>
      </x-slot:footer>

  </x-email-layout-default>
