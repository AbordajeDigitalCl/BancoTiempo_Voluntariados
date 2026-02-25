import ApplicationLogo from '@/Components/ApplicationLogo';
import { Link } from '@inertiajs/react';
import { PropsWithChildren } from 'react';

export default function Guest({ children }: PropsWithChildren) {
  return (
    <div className="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
      <div>
        <Link href="/">
          <ApplicationLogo className="w-20 h-20 fill-current text-gray-500" />
        </Link>
      </div>

      <div className="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        {children}
      </div>

      <footer className="w-full mt-auto border-t border-gray-200 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex items-center justify-center">
          <img
            src="/assets/footer-logo.png"
            alt="Footer logo"
            className="h-12 w-12 object-contain opacity-90"
            loading="lazy"
          />
        </div>
      </footer>
    </div>
  );
}