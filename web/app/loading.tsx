import { Container } from "@/components/Container";

export default function Loading() {
  return (
    <Container className="py-20">
      <div className="animate-pulse space-y-6">
        <div className="h-3 w-24 rounded-full bg-border" />
        <div className="h-12 w-3/4 max-w-3xl rounded-lg bg-border" />
        <div className="h-4 w-2/3 max-w-2xl rounded-full bg-border" />
        <div className="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
          {Array.from({ length: 6 }).map((_, i) => (
            <div
              key={i}
              className="h-44 rounded-2xl border border-border bg-surface"
            />
          ))}
        </div>
      </div>
    </Container>
  );
}
