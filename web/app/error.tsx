"use client";

import { useEffect } from "react";
import Link from "next/link";
import { Container } from "@/components/Container";

export default function GlobalError({
  error,
  reset,
}: {
  error: Error & { digest?: string };
  reset: () => void;
}) {
  useEffect(() => {
    console.error("[GlobalError]", error);
  }, [error]);

  return (
    <Container className="py-32">
      <p className="text-xs uppercase tracking-[0.2em] text-muted">
        Something went wrong
      </p>
      <h1 className="mt-3 text-4xl font-semibold tracking-tight sm:text-5xl">
        We could not load this page.
      </h1>
      <p className="mt-5 max-w-2xl text-lg text-muted">
        This is on us. The error has been logged. You can retry, or head back
        to the homepage and try a different path.
      </p>
      <div className="mt-8 flex flex-wrap gap-3">
        <button
          onClick={reset}
          className="inline-flex items-center rounded-full bg-accent px-5 py-2.5 text-sm font-medium text-white hover:bg-accent-strong transition-colors"
        >
          Try again
        </button>
        <Link
          href="/"
          className="inline-flex items-center rounded-full border border-border px-5 py-2.5 text-sm font-medium hover:border-foreground transition-colors"
        >
          Back to home
        </Link>
      </div>
      {error.digest ? (
        <p className="mt-8 font-mono text-xs text-muted">
          Reference: {error.digest}
        </p>
      ) : null}
    </Container>
  );
}
