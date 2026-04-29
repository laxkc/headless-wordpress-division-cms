import type { Metadata } from "next";
import { getDivisions } from "@/lib/api";
import { Container } from "@/components/Container";
import { DivisionCard } from "@/components/DivisionCard";

export const metadata: Metadata = {
  title: "Practices",
  description:
    "Five Northium practices: Wealth Planning, Tax & Estate, Family CFO, Founder & Equity, and Studio.",
};

export default async function DivisionsPage() {
  const { items } = await getDivisions();

  return (
    <Container className="py-20">
      <div className="max-w-2xl">
        <p className="text-xs uppercase tracking-[0.2em] text-muted">
          Practices
        </p>
        <h1 className="mt-3 text-4xl font-semibold tracking-tight sm:text-5xl">
          Five practices. Five specific jobs.
        </h1>
        <p className="mt-5 text-lg text-muted">
          Each practice focuses on the kind of work it does best. Pick the one
          that matches your situation, or talk to us if you&apos;re not sure
          where you fit.
        </p>
      </div>
      <div className="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        {items.map((division) => (
          <DivisionCard key={division.id} division={division} />
        ))}
      </div>
    </Container>
  );
}
