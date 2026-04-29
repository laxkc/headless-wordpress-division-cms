import type { Metadata } from "next";
import { getAdvisors } from "@/lib/api";
import { Container } from "@/components/Container";
import { AdvisorCard } from "@/components/AdvisorCard";

export const metadata: Metadata = {
  title: "Advisors",
  description:
    "The Northium team. Specialists who lead each practice and the research that informs it.",
};

export default async function AdvisorsPage() {
  const { items } = await getAdvisors();

  return (
    <Container className="py-20">
      <div className="max-w-2xl">
        <p className="text-xs uppercase tracking-[0.2em] text-muted">
          Advisors
        </p>
        <h1 className="mt-3 text-4xl font-semibold tracking-tight sm:text-5xl">
          One advisor per practice. None of them generalists.
        </h1>
        <p className="mt-5 text-lg text-muted">
          Northium runs lean on purpose. Each advisor owns their practice
          end-to-end. You&apos;ll talk to the person doing the work, not a
          relationship manager who hands it off.
        </p>
      </div>
      <div className="mt-12 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        {items.map((advisor) => (
          <AdvisorCard key={advisor.id} advisor={advisor} />
        ))}
      </div>
    </Container>
  );
}
